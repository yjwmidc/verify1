<template>
  <a-config-provider :locale="arcoLocale">
    <div class="page">
      <a-card :title="title" class="card">
        <a-spin :loading="loading" style="width: 100%">
          <a-result v-if="error" status="error" :title="t('load_failed')" :subtitle="error" />

          <template v-else>
            <a-result
              v-if="verified"
              status="success"
              :title="t('title_success')"
              :subtitle="t('verify_success_subtitle')"
            >
              <template #extra>
                <div class="code">{{ code }}</div>
                <a-typography-paragraph v-if="expireMinutes" style="margin: 12px 0 0; text-align: center">
                  {{ t('expire_tip', { minutes: expireMinutes }) }}
                </a-typography-paragraph>
                <a-space direction="vertical" size="medium" fill style="margin-top: 16px">
                  <a-button type="primary" long @click="copyCode">{{ t('copy_code') }}</a-button>
                  <a-button long @click="refreshStatus">{{ t('refresh_status') }}</a-button>
                </a-space>
              </template>
            </a-result>

            <template v-else>
              <a-typography-paragraph style="margin-top: 0; margin-bottom: 12px; white-space: pre-line">
                {{ t('steps') }}
              </a-typography-paragraph>
              <div id="captcha" class="captcha-container"></div>
              <a-space direction="vertical" size="medium" fill>
                <a-button
                  type="primary"
                  long
                  :loading="submitting"
                  :disabled="submitting || !captchaReady"
                  @click="startCaptcha"
                >
                  {{ captchaReady ? t('start_verify') : t('captcha_loading') }}
                </a-button>
                <a-button long :disabled="submitting" @click="refreshStatus">{{ t('refresh_status') }}</a-button>
              </a-space>
            </template>
          </template>
        </a-spin>
      </a-card>
    </div>
  </a-config-provider>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { Message, Modal } from '@arco-design/web-vue';
import { createTranslator, getArcoLocale, getInitialLocale, setLocale as persistLocale } from './i18n';

const ticket = ref('');
const loading = ref(true);
const error = ref('');
const verified = ref(false);
const code = ref('');
const captchaId = ref('');
const expireMinutes = ref(null);
const captchaReady = ref(false);
const submitting = ref(false);
const locale = ref('zh-CN');

let captchaObj = null;

const t = createTranslator(() => locale.value);
const arcoLocale = computed(() => getArcoLocale(locale.value));
const title = computed(() => (verified.value ? t('title_success') : t('title_default')));

function parseTicketFromPath() {
  const search = window.location.search || '';
  const sp = new URLSearchParams(search);
  const q = sp.get('ticket');
  if (q) return q;

  const path = window.location.pathname || '';
  const m1 = path.match(/\/v\/([^/?#]+)/);
  if (m1 && m1[1]) return decodeURIComponent(m1[1]);

  const hash = window.location.hash || '';
  const m2 = hash.match(/#\/v\/([^/?#]+)/);
  return m2 && m2[1] ? decodeURIComponent(m2[1]) : '';
}

function toFormBody(obj) {
  const params = new URLSearchParams();
  Object.keys(obj).forEach((k) => params.append(k, obj[k] == null ? '' : String(obj[k])));
  return params.toString();
}

async function copyText(text) {
  if (navigator.clipboard && window.isSecureContext) {
    await navigator.clipboard.writeText(text);
    return;
  }

  const ta = document.createElement('textarea');
  ta.value = text;
  ta.style.position = 'fixed';
  ta.style.opacity = '0';
  document.body.appendChild(ta);
  ta.focus();
  ta.select();
  const ok = document.execCommand('copy');
  document.body.removeChild(ta);
  if (!ok) throw new Error('copy failed');
}

function showExpired() {
  error.value = t('link_expired_or_missing');
  verified.value = false;
  code.value = '';
  expireMinutes.value = null;
}

function initGeetest() {
  if (!captchaId.value) return;

  if (typeof window.initGeetest4 === 'undefined') {
    error.value = t('captcha_component_load_failed');
    captchaReady.value = false;
    return;
  }

  captchaReady.value = false;
  captchaObj = null;
  const container = document.querySelector('#captcha');
  if (container) container.innerHTML = '';

  window.initGeetest4(
    {
      captchaId: captchaId.value,
      product: 'bind',
      language: 'zh-cn',
      timeout: 10000
    },
    (obj) => {
      captchaObj = obj;

      try {
        obj.appendTo('#captcha');
      } catch (e) {}

      obj
        .onReady(() => {
          captchaReady.value = true;
        })
        .onError(() => {
          error.value = t('captcha_init_failed');
          captchaReady.value = false;
        })
        .onSuccess(() => {
          const result = captchaObj && captchaObj.getValidate ? captchaObj.getValidate() : null;
          if (!result) {
            Message.error(t('please_complete_captcha'));
            return;
          }
          submitVerification(result);
        })
        .onClose(() => {
          submitting.value = false;
        });
    }
  );
}

function startCaptcha() {
  if (!captchaReady.value || !captchaObj) {
    Message.warning(t('captcha_loading_wait'));
    return;
  }
  try {
    captchaObj.showCaptcha();
  } catch (e) {
    Message.error(t('captcha_error_refresh'));
  }
}

async function submitVerification(geetestResult) {
  submitting.value = true;

  try {
    const res = await fetch('/verify/callback', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: toFormBody({
        ticket: ticket.value,
        lot_number: geetestResult.lot_number,
        captcha_output: geetestResult.captcha_output,
        pass_token: geetestResult.pass_token,
        gen_time: geetestResult.gen_time
      })
    });

    const data = await res.json();
    if (data && data.code === 0 && data.data && data.data.code) {
      verified.value = true;
      code.value = String(data.data.code);
      Message.success(t('verify_success'));
      try {
        await copyText(code.value);
        Message.success(t('code_copied_paste'));
      } catch (e) {}
      return;
    }

    Message.error((data && data.msg) || t('verify_failed_retry'));
    submitting.value = false;
    if (captchaObj) {
      try {
        captchaObj.reset();
      } catch (e) {}
    }
  } catch (e) {
    Message.error(t('network_error_retry'));
    submitting.value = false;
  }
}

async function copyCode() {
  if (!code.value) return;
  try {
    await copyText(code.value);
    Message.success(t('code_copied'));
  } catch (e) {
    Modal.info({
      title: t('copy_failed'),
      content: t('copy_failed_manual_prefix') + code.value,
      hideCancel: true
    });
  }
}

async function refreshStatus() {
  loading.value = true;
  error.value = '';

  try {
    const res = await fetch('/verify/status/' + encodeURIComponent(ticket.value), { method: 'GET' });
    const data = await res.json();

    if (!data || typeof data.code === 'undefined') {
      error.value = t('server_response_invalid');
      return;
    }

    if (data.code === 404) {
      showExpired();
      return;
    }

    if (data.code !== 0) {
      error.value = data.msg || t('load_failed');
      return;
    }

    const expire = data.data && typeof data.data.expire_minutes !== 'undefined' ? Number(data.data.expire_minutes) : null;
    expireMinutes.value = Number.isFinite(expire) && expire > 0 ? Math.ceil(expire) : null;

    if (data.data && data.data.verified) {
      verified.value = true;
      code.value = String(data.data.code || '');
      return;
    }

    verified.value = false;
    code.value = '';
    captchaId.value = (data.data && data.data.captcha_id) || '';
    initGeetest();
  } catch (e) {
    error.value = '网络异常，请稍后重试';
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  locale.value = persistLocale(getInitialLocale());
  ticket.value = parseTicketFromPath();
  if (!ticket.value) {
    loading.value = false;
    error.value = t('invalid_link');
    return;
  }
  refreshStatus();
});
</script>

<style scoped>
.page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-bg-1);
}

.card {
  width: 100%;
  max-width: 520px;
}

.code {
  font-size: 40px;
  font-weight: 800;
  letter-spacing: 10px;
  text-align: center;
  background: var(--color-bg-2);
  padding: 16px 18px;
}

.captcha-container {
  height: 0;
  overflow: hidden;
}
</style>
