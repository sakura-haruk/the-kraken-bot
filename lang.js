const translations = {
  fr: {
    title: "The Kraken Bot",
    subtitle: "Un bot Discord complet pour la musique, la modération, les sondages, et plus encore.",
    btn_features: "Fonctionnalités",
    btn_invite: "Inviter le bot",
    btn_dashboard: "Dashboard",
    btn_support: "Serveur Support",
    footer_privacy: "Politique de confidentialité",
    footer_terms: "Conditions d'utilisation"
  },
  en: {
    title: "The Kraken Bot",
    subtitle: "A complete Discord bot for music, moderation, polls, and more.",
    btn_features: "Features",
    btn_invite: "Invite Bot",
    btn_dashboard: "Dashboard",
    btn_support: "Support Server",
    footer_privacy: "Privacy Policy",
    footer_terms: "Terms of Service"
  },
  es: {
    title: "The Kraken Bot",
    subtitle: "Un bot de Discord completo para música, moderación, encuestas y más.",
    btn_features: "Características",
    btn_invite: "Invitar al bot",
    btn_dashboard: "Panel de control",
    btn_support: "Servidor de soporte",
    footer_privacy: "Política de privacidad",
    footer_terms: "Términos de servicio"
  }
};

function changeLang(lang) {
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    el.textContent = translations[lang][key] || el.textContent;
  });
  localStorage.setItem('siteLang', lang);
}
