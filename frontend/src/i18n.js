import enUS from '@arco-design/web-vue/es/locale/lang/en-us';
import jaJP from '@arco-design/web-vue/es/locale/lang/ja-jp';
import zhCN from '@arco-design/web-vue/es/locale/lang/zh-cn';
import zhTW from '@arco-design/web-vue/es/locale/lang/zh-tw';

const supportedLocales = ['zh-CN', 'zh-TW', 'en-US', 'ja-JP'];

const messages = {
  'zh-CN': {
    title_default: '入群验证',
    title_success: '验证成功',
    load_failed: '加载失败',
    verify_success_subtitle: '请在群内发送下方绑定码完成验证',
    expire_tip: '绑定码{minutes}分钟内有效，请及时使用。',
    copy_code: '复制绑定码',
    refresh_status: '刷新状态',
    steps: '1. 点击“开始验证”完成安全验证\n2. 获取绑定码后，在群内发送即可',
    start_verify: '开始验证',
    captcha_loading: '验证码加载中…',
    captcha_component_load_failed: '验证码组件加载失败，请刷新页面重试',
    captcha_init_failed: '验证码初始化失败，请刷新页面重试',
    please_complete_captcha: '请先完成验证',
    captcha_loading_wait: '验证码正在加载，请稍候…',
    captcha_error_refresh: '验证码出错，请刷新页面重试',
    verify_success: '验证成功',
    code_copied_paste: '绑定码已复制，可直接在群内粘贴发送',
    verify_failed_retry: '验证失败，请重试',
    network_error_retry: '网络异常，请稍后重试',
    code_copied: '绑定码已复制',
    copy_failed: '复制失败',
    copy_failed_manual_prefix: '请手动复制绑定码：',
    server_response_invalid: '服务器响应异常',
    link_expired_or_missing: '验证链接已过期或不存在',
    invalid_link: '无效的验证链接'
  },
  'zh-TW': {
    title_default: '入群驗證',
    title_success: '驗證成功',
    load_failed: '載入失敗',
    verify_success_subtitle: '請在群內發送下方綁定碼完成驗證',
    expire_tip: '綁定碼{minutes}分鐘內有效，請及時使用。',
    copy_code: '複製綁定碼',
    refresh_status: '重新整理狀態',
    steps: '1. 點擊「開始驗證」完成安全驗證\n2. 取得綁定碼後，在群內發送即可',
    start_verify: '開始驗證',
    captcha_loading: '驗證碼載入中…',
    captcha_component_load_failed: '驗證碼元件載入失敗，請重新整理頁面重試',
    captcha_init_failed: '驗證碼初始化失敗，請重新整理頁面重試',
    please_complete_captcha: '請先完成驗證',
    captcha_loading_wait: '驗證碼正在載入，請稍候…',
    captcha_error_refresh: '驗證碼發生錯誤，請重新整理頁面重試',
    verify_success: '驗證成功',
    code_copied_paste: '驗證碼已複製，可直接在群內貼上發送',
    verify_failed_retry: '驗證失敗，請重試',
    network_error_retry: '網路異常，請稍後重試',
    code_copied: '驗證碼已複製',
    copy_failed: '複製失敗',
    copy_failed_manual_prefix: '請手動複製綁定碼：',
    server_response_invalid: '伺服器回應異常',
    link_expired_or_missing: '驗證連結已過期或不存在',
    invalid_link: '無效的驗證連結'
  },
  'en-US': {
    title_default: 'Group Verification',
    title_success: 'Verified',
    load_failed: 'Load failed',
    verify_success_subtitle: 'Send the binding code below in the group to finish verification',
    expire_tip: 'The binding code expires in {minutes} minutes.',
    copy_code: 'Copy Binding Code',
    refresh_status: 'Refresh',
    steps: '1. Click "Start Verification" to complete the challenge\n2. Send the binding code in the group chat',
    start_verify: 'Start Verification',
    captcha_loading: 'Loading…',
    captcha_component_load_failed: 'Captcha script failed to load. Please refresh and try again.',
    captcha_init_failed: 'Captcha initialization failed. Please refresh and try again.',
    please_complete_captcha: 'Please complete the challenge first',
    captcha_loading_wait: 'Captcha is loading, please wait…',
    captcha_error_refresh: 'Captcha error. Please refresh and try again.',
    verify_success: 'Verification succeeded',
    code_copied_paste: 'Binding code copied. You can paste it into the group chat.',
    verify_failed_retry: 'Verification failed. Please try again.',
    network_error_retry: 'Network error. Please try again later.',
    code_copied: 'Binding code copied',
    copy_failed: 'Copy failed',
    copy_failed_manual_prefix: 'Please copy the binding code manually: ',
    server_response_invalid: 'Invalid server response',
    link_expired_or_missing: 'This link is expired or does not exist',
    invalid_link: 'Invalid link'
  },
  'ja-JP': {
    title_default: 'グループ認証',
    title_success: '認証成功',
    load_failed: '読み込みに失敗しました',
    verify_success_subtitle: '下のバインドコードをグループに送信して認証を完了してください',
    expire_tip: 'このバインドコードの有効期限は{minutes}分です。',
    copy_code: 'バインドコードをコピー',
    refresh_status: '更新',
    steps: '1. 「認証開始」をクリックして認証を完了します\n2. バインドコードをグループに送信してください',
    start_verify: '認証開始',
    captcha_loading: '読み込み中…',
    captcha_component_load_failed: 'Captcha の読み込みに失敗しました。ページを更新して再試行してください。',
    captcha_init_failed: 'Captcha の初期化に失敗しました。ページを更新して再試行してください。',
    please_complete_captcha: '先に認証を完了してください',
    captcha_loading_wait: 'Captcha を読み込み中です。しばらくお待ちください…',
    captcha_error_refresh: 'Captcha エラー。ページを更新して再試行してください。',
    verify_success: '認証に成功しました',
    code_copied_paste: 'バインドコードをコピーしました。グループに貼り付けて送信できます。',
    verify_failed_retry: '認証に失敗しました。再試行してください。',
    network_error_retry: 'ネットワークエラー。しばらくしてから再試行してください。',
    code_copied: 'バインドコードをコピーしました',
    copy_failed: 'コピーに失敗しました',
    copy_failed_manual_prefix: 'バインドコードを手動でコピーしてください：',
    server_response_invalid: 'サーバー応答が不正です',
    link_expired_or_missing: 'リンクの有効期限が切れているか存在しません',
    invalid_link: '無効なリンク'
  }
};

function normalizeLocale(locale) {
  const raw = String(locale || '').trim();
  if (!raw) return 'zh-CN';

  const lowered = raw.toLowerCase();
  if (lowered === 'zh-cn' || lowered === 'zh-hans' || lowered === 'zh') return 'zh-CN';
  if (lowered === 'zh-tw' || lowered === 'zh-hant' || lowered === 'zh-hk' || lowered === 'zh-mo') return 'zh-TW';
  if (lowered.startsWith('en')) return 'en-US';
  if (lowered.startsWith('ja')) return 'ja-JP';

  const exact = supportedLocales.find((x) => x.toLowerCase() === lowered);
  return exact || 'zh-CN';
}

function readLocaleFromQuery() {
  if (typeof window === 'undefined') return '';
  const sp = new URLSearchParams(window.location.search || '');
  return sp.get('lang') || sp.get('locale') || '';
}

function readLocaleFromStorage() {
  if (typeof window === 'undefined') return '';
  try {
    return window.localStorage.getItem('locale') || '';
  } catch (e) {
    return '';
  }
}

function writeLocaleToStorage(locale) {
  if (typeof window === 'undefined') return;
  try {
    window.localStorage.setItem('locale', locale);
  } catch (e) {}
}

function detectBrowserLocale() {
  if (typeof navigator === 'undefined') return 'zh-CN';
  const langs = Array.isArray(navigator.languages) ? navigator.languages : [];
  const first = langs[0] || navigator.language || '';
  return normalizeLocale(first);
}

export function getInitialLocale() {
  const q = normalizeLocale(readLocaleFromQuery());
  if (q) return q;
  const stored = normalizeLocale(readLocaleFromStorage());
  if (stored) return stored;
  return detectBrowserLocale();
}

export function setLocale(locale) {
  const next = normalizeLocale(locale);
  writeLocaleToStorage(next);
  if (typeof document !== 'undefined' && document.documentElement) {
    document.documentElement.lang = next;
  }
  return next;
}

export function getArcoLocale(locale) {
  const l = normalizeLocale(locale);
  if (l === 'en-US') return enUS;
  if (l === 'ja-JP') return jaJP;
  if (l === 'zh-TW') return zhTW;
  return zhCN;
}

export function createTranslator(getLocale) {
  return function t(key, params) {
    const locale = normalizeLocale(getLocale());
    const dict = messages[locale] || messages['zh-CN'];
    let text = dict[key] || messages['zh-CN'][key] || key;
    if (params && typeof params === 'object') {
      Object.keys(params).forEach((k) => {
        text = text.replaceAll('{' + k + '}', String(params[k]));
      });
    }
    return text;
  };
}
