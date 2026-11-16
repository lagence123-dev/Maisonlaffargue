# Module de Rétractation de Commande pour PrestaShop

## Vue d'ensemble
Le Module de Rétractation de Commande permet aux clients d'exercer leur droit de rétractation pour les commandes conformément aux lois européennes de protection des consommateurs. Le formulaire public utilise par défaut le nom du client + l'identifiant de commande, enregistre la demande pour revue manuelle et envoie les confirmations par e-mail.

## Fonctionnalités
- **Conformité UE**: Entièrement conforme à la Directive 2011/83/UE sur les droits des consommateurs
- **Création Automatique du Statut de Commande**: Crée automatiquement le statut de commande "Rétractation Demandée" lors de l'installation
- **Calcul Automatique de la Période**: Calcule la période de rétractation en fonction de la date de commande et du délai de livraison estimé
- **Interface Conviviale pour le Client**: Formulaire simple pour que les clients soumettent des demandes de rétractation
- **Notifications par E-mail**: Notifications automatiques pour les clients et les administrateurs
- **Support Multilingue**: Disponible en allemand, anglais, français, italien, espagnol, polonais, roumain et néerlandais
- **Paramètres Configurables**: Période de rétractation et délais de livraison personnalisables
- **Vérification Flexible**: Nom + identifiant de commande recommandé, identifiant seul optionnel, e-mail + identifiant en mode legacy
- **Revue Manuelle par Défaut**: Les nouvelles installations enregistrent les demandes pour décision dans le Back Office

## Prérequis
- PrestaShop 1.7.8 ou supérieur (testé avec PrestaShop 8.x et 9.x)
- PHP 7.4 ou supérieur (PHP 8.x pris en charge)
- Certificat SSL recommandé pour les soumissions de formulaires sécurisées

## Installation

### Méthode 1: Via le Panneau d'Administration PrestaShop
1. Téléchargez le fichier ZIP du module
2. Accédez à **Modules** → **Gestionnaire de Modules** dans votre administration PrestaShop
3. Cliquez sur **Télécharger un module**
4. Sélectionnez le fichier ZIP et téléchargez-le
5. Le module s'installera automatiquement
6. Lors de l'installation, un nouveau statut de commande **"Rétractation Demandée"** est automatiquement créé

### Méthode 2: Installation Manuelle
1. Téléchargez et extrayez les fichiers du module
2. Téléchargez le dossier `orderwithdrawl` dans le répertoire `/modules/`
3. Accédez à **Modules** → **Gestionnaire de Modules**
4. Recherchez "Order Withdrawal"
5. Cliquez sur **Installer**
6. Lors de l'installation, un nouveau statut de commande **"Rétractation Demandée"** est automatiquement créé

**Note**: Le module crée automatiquement le statut de commande "Rétractation Demandée" avec une couleur orange (#FFA500) lors de l'installation. Ce statut est utilisé pour suivre les commandes où les clients ont demandé une rétractation.

## Configuration

Après l'installation, configurez le module en cliquant sur **Configurer**:

### Paramètres de Base
- **Statut du Module**: Activer/désactiver le module
- **Période de Rétractation (jours)**: Définissez le nombre de jours dont disposent les clients pour se rétracter (par défaut: 14 jours selon la loi de l'UE)
- **Délai de Livraison Estimé (jours)**: Délai de livraison moyen pour calculer la date limite de rétractation
- **Vérification du Client**: Recommandé nom + identifiant de commande; optionnel identifiant seul; legacy e-mail + identifiant
- **Mode d'Évaluation**: Revue manuelle par défaut ou contrôle automatique de la période
- **Notifications E-mail Administrateur Supplémentaires**: Ajoutez des adresses e-mail supplémentaires pour les notifications de rétractation (séparées par des virgules)

## Utilisation

### Pour les Clients
1. Cliquez sur le lien **"Se rétracter du contrat"** dans la section Mon Compte (généralement visible dans la zone du pied de page)
2. Le formulaire de demande de rétractation s'ouvre
3. Saisissez l'**identifiant de commande configuré** et, par défaut, votre **nom**. Certaines boutiques peuvent utiliser l'identifiant seul ou le mode legacy avec e-mail.
4. Cliquez sur **Soumettre la Demande de Rétractation**
5. Le système valide:
   - La commande existe et correspond au mode de vérification configuré
   - La période de rétractation est contrôlée automatiquement uniquement si ce mode est activé
6. Après une soumission réussie:
   - Recevez un message de confirmation à l'écran
   - Recevez un e-mail de confirmation
   - Le statut de la commande change en "Rétractation Demandée" pour revue manuelle

### Pour les Administrateurs
1. Les demandes de rétractation déclenchent des notifications par e-mail
2. Le statut de la commande change en "Rétractation Demandée" (si configuré)
3. Traitez les remboursements selon la politique de votre boutique
4. Mettez à jour le statut de la commande en conséquence

## Modèles d'E-mail

Le module envoie des e-mails automatisés pour:
- **Confirmation Client**: Envoyé lorsque la demande de rétractation est soumise
- **Notification Administrateur**: Envoyé au(x) administrateur(s) de la boutique lorsque la demande est reçue

## Dépannage

### Problèmes Courants

**Message "La période de rétractation a expiré"**
- La période de rétractation est passée en fonction de la date de commande + délai de livraison + jours de rétractation
- Vérifiez vos paramètres de configuration

**Erreur "Statut de rétractation non configuré"**
- Cette erreur ne devrait pas se produire si le module a été installé correctement
- Le statut "Rétractation Demandée" est créé automatiquement lors de l'installation
- Si cette erreur apparaît, essayez de réinstaller le module

**Notifications par e-mail non reçues**
- Vérifiez la configuration des e-mails dans PrestaShop
- Vérifiez les dossiers de spam
- Assurez-vous que les adresses e-mail sont correctement formatées

## Conformité Légale

Ce module aide à se conformer à:
- **Directive 2011/83/UE de l'UE** sur les droits des consommateurs
- **BGB allemand § 355** (Widerrufsrecht)
- Mises en œuvre nationales des lois de protection des consommateurs de l'UE

**Important**: Ce module fournit le cadre technique pour les demandes de rétractation. Les commerçants sont responsables de:
- Traiter les remboursements dans les délais légaux
- Maintenir une documentation appropriée
- Assurer la conformité légale complète avec les réglementations locales

## Captures d'Écran

1. **Page de Configuration du Module** (`img/en/screenshot_1_config.png`)
   - Affiche l'interface de configuration principale avec tous les paramètres visibles

2. **Section Pied de Page Mon Compte** (`img/en/screenshot_2_footer_link.png`)
   - Affiche le lien "Se rétracter du contrat" dans la section Mon Compte/pied de page

3. **Formulaire de Demande de Rétractation** (`img/en/screenshot_3_withdrawal_form.png`)
   - Affiche le formulaire où les clients saisissent l'identifiant de commande et les données requises par le mode de vérification

4. **Changement de Statut de Commande** (`img/en/screenshot_8_order_status.png`)
   - Affiche une commande avec le statut "Rétractation Demandée" dans le panneau d'administration

## Support

Pour le support technique, veuillez utiliser le canal de support de la marketplace lié à votre commande du module.
- Questions juridiques: Consultez votre conseiller juridique
- Mises à jour du module: Consultez régulièrement la page du module

## Journal des Modifications

### Version 1.4.0 (juin 2026)
- Nouvelle option facultative dans le Back Office pour afficher les produits de la commande après vérification des informations client
- Le client peut sélectionner toute la commande ou des articles précis avec une quantité valide
- La liste des produits est chargée côté serveur et n'utilise pas AJAX
- L'option est désactivée par défaut et conserve les paramètres existants
- Aucun changement de schéma de base de données ; compatibilité maintenue avec PrestaShop 1.7.8, 8.x et 9.x avec PHP 7.4+

### Version 1.3.1 (mai 2026)
- Version publique 1.3.1 avec prise en charge structurée des rétractations partielles
- Le formulaire public permet de choisir entre la commande complète et des articles/services sélectionnés
- La rétractation partielle utilise des lignes répétables avec nom d'article, numéro d'article et quantité
- Aucun motif de rétractation n'est demandé ; les champs servent uniquement à identifier le contrat ou la partie du contrat
- Les e-mails d'accusé de réception client et administrateur incluent la portée de la rétractation et les détails transmis
- Les détails de rétractation partielle sont enregistrés comme message de commande pour vérification dans le Back Office
- Aucun changement de schéma de base de données ; les paramètres existants sont conservés

### Version 1.3.0 (mai 2026)
- Version publique 1.3.0
- Revue manuelle par défaut pour les nouvelles installations
- Vérification recommandée par nom du client + identifiant de commande
- Mode optionnel identifiant seul et mode legacy e-mail + identifiant
- Le formulaire public n'affiche plus le bloc `<details>` de politique de rétractation
- Les travaux internes précédents de mise à jour 1.3.x ont été intégrés dans `upgrade-1.3.0.php`

**Mise à jour de 1.2.4 vers 1.3.0 :**
- Mise à jour directe depuis 1.2.4 vers 1.3.0 ; les changements internes précédents de 1.3.x sont regroupés dans cette version publique.
- Aucun changement de schéma de base de données ; les paramètres existants du marchand sont conservés.
- Nouveaux paramètres par défaut manquants : revue manuelle, vérification par nom + identifiant de commande, recherche par numéro/référence de commande et fallback PHP mail activé.
- Les e-mails d'accusé de réception confirment la demande de rétractation et les informations transmises, sans suggérer une décision finale.
- Nouvelles options Back Office pour la vérification client, la recherche de commande, le fallback e-mail, l'aperçu des e-mails, l'envoi de test et la réinitialisation.
- Formulaire public renforcé : token CSRF, honeypot, limite par IP, prévention des doublons et notes client sécurisées pour HTML.
- Envoi d'e-mails plus fiable via MailDispatcher central, diagnostics et fallback PHP mail.
- Templates d'e-mail et traductions complétés pour toutes les langues livrées.

### Version 1.0.0 (2024)
- Version initiale
- Création automatique du statut de commande "Rétractation Demandée"
- Support multilingue (8 langues)
- Système de notification par e-mail
- Périodes de rétractation configurables
- Conformité à la directive de l'UE

## Licence

Ce module est un logiciel propriétaire. Tous droits réservés.

---

*Ce module est fourni tel quel. Bien qu'il vise à aider à la conformité UE, les commerçants sont finalement responsables de s'assurer que leur boutique répond à toutes les exigences légales.*
