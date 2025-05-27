<?php

/**
 * Système de traduction pour Discord Bot
 * Gère les langues multiples avec fichiers JSON
 */

class TranslationManager
{
    private $translations = [];
    private $defaultLanguage = 'en';
    private $currentLanguage = 'en';
    private $translationsPath = 'translations/';

    public function __construct($defaultLang = 'en', $translationsPath = 'translations/')
    {
        $this->defaultLanguage = $defaultLang;
        $this->currentLanguage = $defaultLang;
        $this->translationsPath = $translationsPath;
        $this->loadTranslations();
    }

    /**
     * Charge toutes les traductions depuis les fichiers JSON
     */
    private function loadTranslations()
    {
        if (!is_dir($this->translationsPath)) {
            mkdir($this->translationsPath, 0755, true);
        }

        $files = glob($this->translationsPath . '*.json');
        
        foreach ($files as $file) {
            $lang = basename($file, '.json');
            $content = file_get_contents($file);
            $this->translations[$lang] = json_decode($content, true) ?? [];
        }

        // Créer les fichiers de base s'ils n'existent pas
        $this->createDefaultTranslations();
    }

    /**
     * Crée les traductions par défaut si elles n'existent pas
     */
    private function createDefaultTranslations()
    {
        $defaultTranslations = [
            'en' => [
                'welcome' => 'Welcome to the server!',
                'goodbye' => 'Goodbye!',
                'help' => 'Available commands:',
                'error' => 'An error occurred',
                'success' => 'Operation completed successfully',
                'permission_denied' => 'You don\'t have permission to use this command',
                'user_not_found' => 'User not found',
                'invalid_command' => 'Invalid command',
                'language_changed' => 'Language changed to English',
                'available_languages' => 'Available languages: {languages}',
                'music' => [
                    'playing' => 'Now playing: {song}',
                    'paused' => 'Music paused',
                    'resumed' => 'Music resumed',
                    'stopped' => 'Music stopped',
                    'queue_empty' => 'Queue is empty',
                    'added_to_queue' => 'Added to queue: {song}'
                ]
            ],
            'fr' => [
                'welcome' => 'Bienvenue sur le serveur !',
                'goodbye' => 'Au revoir !',
                'help' => 'Commandes disponibles :',
                'error' => 'Une erreur s\'est produite',
                'success' => 'Opération réalisée avec succès',
                'permission_denied' => 'Vous n\'avez pas la permission d\'utiliser cette commande',
                'user_not_found' => 'Utilisateur introuvable',
                'invalid_command' => 'Commande invalide',
                'language_changed' => 'Langue changée vers le français',
                'available_languages' => 'Langues disponibles : {languages}',
                'music' => [
                    'playing' => 'En cours de lecture : {song}',
                    'paused' => 'Musique en pause',
                    'resumed' => 'Musique reprise',
                    'stopped' => 'Musique arrêtée',
                    'queue_empty' => 'File d\'attente vide',
                    'added_to_queue' => 'Ajouté à la file : {song}'
                ]
            ],
            'es' => [
                'welcome' => '¡Bienvenido al servidor!',
                'goodbye' => '¡Adiós!',
                'help' => 'Comandos disponibles:',
                'error' => 'Ha ocurrido un error',
                'success' => 'Operación completada exitosamente',
                'permission_denied' => 'No tienes permiso para usar este comando',
                'user_not_found' => 'Usuario no encontrado',
                'invalid_command' => 'Comando inválido',
                'language_changed' => 'Idioma cambiado a español',
                'available_languages' => 'Idiomas disponibles: {languages}',
                'music' => [
                    'playing' => 'Reproduciendo: {song}',
                    'paused' => 'Música pausada',
                    'resumed' => 'Música reanudada',
                    'stopped' => 'Música detenida',
                    'queue_empty' => 'Cola vacía',
                    'added_to_queue' => 'Agregado a la cola: {song}'
                ]
            ]
        ];

        foreach ($defaultTranslations as $lang => $translations) {
            $file = $this->translationsPath . $lang . '.json';
            if (!file_exists($file)) {
                file_put_contents($file, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $this->translations[$lang] = $translations;
            }
        }
    }

    /**
     * Définit la langue courante
     */
    public function setLanguage($language)
    {
        if (isset($this->translations[$language])) {
            $this->currentLanguage = $language;
            return true;
        }
        return false;
    }

    /**
     * Obtient la langue courante
     */
    public function getCurrentLanguage()
    {
        return $this->currentLanguage;
    }

    /**
     * Obtient toutes les langues disponibles
     */
    public function getAvailableLanguages()
    {
        return array_keys($this->translations);
    }

    /**
     * Traduit une clé avec des paramètres optionnels
     */
    public function translate($key, $params = [], $language = null)
    {
        $lang = $language ?? $this->currentLanguage;
        
        // Si la langue n'existe pas, utiliser la langue par défaut
        if (!isset($this->translations[$lang])) {
            $lang = $this->defaultLanguage;
        }

        $translation = $this->getNestedValue($this->translations[$lang], $key);
        
        // Si la traduction n'existe pas dans la langue courante, essayer la langue par défaut
        if ($translation === null && $lang !== $this->defaultLanguage) {
            $translation = $this->getNestedValue($this->translations[$this->defaultLanguage], $key);
        }

        // Si toujours pas trouvé, retourner la clé
        if ($translation === null) {
            return $key;
        }

        // Remplacer les paramètres
        foreach ($params as $param => $value) {
            $translation = str_replace('{' . $param . '}', $value, $translation);
        }

        return $translation;
    }

    /**
     * Obtient une valeur imbriquée dans un tableau
     */
    private function getNestedValue($array, $key)
    {
        $keys = explode('.', $key);
        $value = $array;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Ajoute ou met à jour une traduction
     */
    public function addTranslation($language, $key, $value)
    {
        if (!isset($this->translations[$language])) {
            $this->translations[$language] = [];
        }

        $this->setNestedValue($this->translations[$language], $key, $value);
        $this->saveTranslation($language);
    }

    /**
     * Définit une valeur imbriquée dans un tableau
     */
    private function setNestedValue(&$array, $key, $value)
    {
        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }

        $current = $value;
    }

    /**
     * Sauvegarde les traductions d'une langue
     */
    private function saveTranslation($language)
    {
        $file = $this->translationsPath . $language . '.json';
        file_put_contents($file, json_encode($this->translations[$language], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

/**
 * Gestionnaire de langues par utilisateur/serveur
 */
class UserLanguageManager
{
    private $userLanguages = [];
    private $serverLanguages = [];
    private $dataFile = 'data/user_languages.json';

    public function __construct($dataFile = 'data/user_languages.json')
    {
        $this->dataFile = $dataFile;
        $this->loadData();
    }

    /**
     * Charge les données depuis le fichier
     */
    private function loadData()
    {
        if (file_exists($this->dataFile)) {
            $data = json_decode(file_get_contents($this->dataFile), true);
            $this->userLanguages = $data['users'] ?? [];
            $this->serverLanguages = $data['servers'] ?? [];
        }
    }

    /**
     * Sauvegarde les données
     */
    private function saveData()
    {
        $dir = dirname($this->dataFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $data = [
            'users' => $this->userLanguages,
            'servers' => $this->serverLanguages
        ];

        file_put_contents($this->dataFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Définit la langue d'un utilisateur
     */
    public function setUserLanguage($userId, $language)
    {
        $this->userLanguages[$userId] = $language;
        $this->saveData();
    }

    /**
     * Obtient la langue d'un utilisateur
     */
    public function getUserLanguage($userId)
    {
        return $this->userLanguages[$userId] ?? null;
    }

    /**
     * Définit la langue par défaut d'un serveur
     */
    public function setServerLanguage($serverId, $language)
    {
        $this->serverLanguages[$serverId] = $language;
        $this->saveData();
    }

    /**
     * Obtient la langue d'un serveur
     */
    public function getServerLanguage($serverId)
    {
        return $this->serverLanguages[$serverId] ?? null;
    }

    /**
     * Obtient la langue appropriée pour un utilisateur dans un serveur
     */
    public function getLanguageForUser($userId, $serverId = null)
    {
        // Priorité : langue utilisateur > langue serveur > langue par défaut
        return $this->getUserLanguage($userId) 
            ?? ($serverId ? $this->getServerLanguage($serverId) : null) 
            ?? 'en';
    }
}

/**
 * Classe principale pour gérer les traductions dans le bot
 */
class BotTranslator
{
    private $translationManager;
    private $userLanguageManager;

    public function __construct()
    {
        $this->translationManager = new TranslationManager();
        $this->userLanguageManager = new UserLanguageManager();
    }

    /**
     * Traduit un message pour un utilisateur spécifique
     */
    public function translateForUser($userId, $serverId, $key, $params = [])
    {
        $language = $this->userLanguageManager->getLanguageForUser($userId, $serverId);
        return $this->translationManager->translate($key, $params, $language);
    }

    /**
     * Commande pour changer la langue d'un utilisateur
     */
    public function changeUserLanguage($userId, $serverId, $newLanguage)
    {
        $availableLanguages = $this->translationManager->getAvailableLanguages();
        
        if (!in_array($newLanguage, $availableLanguages)) {
            return $this->translateForUser($userId, $serverId, 'available_languages', [
                'languages' => implode(', ', $availableLanguages)
            ]);
        }

        $this->userLanguageManager->setUserLanguage($userId, $newLanguage);
        $this->translationManager->setLanguage($newLanguage);
        
        return $this->translationManager->translate('language_changed', [], $newLanguage);
    }

    /**
     * Obtient le gestionnaire de traductions
     */
    public function getTranslationManager()
    {
        return $this->translationManager;
    }

    /**
     * Obtient le gestionnaire de langues utilisateur
     */
    public function getUserLanguageManager()
    {
        return $this->userLanguageManager;
    }
}

// Exemple d'utilisation
/*
$translator = new BotTranslator();

// Traduire un message pour un utilisateur
echo $translator->translateForUser('user123', 'server456', 'welcome') . "\n";

// Changer la langue d'un utilisateur
echo $translator->changeUserLanguage('user123', 'server456', 'fr') . "\n";

// Traduire avec des paramètres
echo $translator->translateForUser('user123', 'server456', 'music.playing', ['song' => 'My Song']) . "\n";
*/

?>
