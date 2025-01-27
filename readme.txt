=== Creactiveweb ===
Contributors: creactive
Donate link: https://creactiveweb.com
Tags: woocommerce, b2bking, sku, personnalisation, my account
Requires at least: 5.8
Tested up to: 6.3
Requires PHP: 7.4
Stable tag: 2.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin personnalisé pour Nillor, incluant :
- Recherche par SKU (avec variations)
- Personnalisation PDF B2BKing
- Onglets et infos "Mon Compte"
- Shortcodes connexion
- Widget tableau de bord custom
- Paramètres d’options via une page d’admin

== Description ==

Plugin complet de personnalisation. Compatible WooCommerce.

== Installation ==
1. Téléchargez le plugin et extrayez l’archive.
2. Envoyez le dossier `creactiveweb` dans `wp-content/plugins/` ou installez-le en .zip via l’admin WordPress.
3. Activez le plugin dans "Extensions".

== Changelog ==
= 2.0 =
* Réorganisation des fichiers en classes.
* Ajout d'une page de paramètres.

= 2.1 =
* Ajout de toggles pour chaque fonctionnalité
* Ajout de styles custom pour la page de réglages

= 2.2 =
* Ajout d’une page d’options plus stylisée
* Personnalisation des onglets "Mon compte" et infos

= 2.3 = 
* Changement de fonctionnement pour la recherche SKU

= 2.3.1 = 
* Ajout du selecteur logique de recherche SKU sur le back-office.

= 2.3.2 = 
* Correction erreur fatale 'recherche par SKU'

= 2.3.3 = 
* Ajout d'une nouvelle version de recherche by SKU

= 2.3.3 = 
* Ajout de l'option de recherche de variations spécifique manquante.

= 2.3.4 = 
* Roll-back methode recherche SKU - fonctionnement ok.

= 2.3.5 = 
* Simplification du tableau de bord. 
* Suppression redondance toggle activation de la recherche by SKU

= 2.3.6 = 
* Redirection page de devis lorsque pas de prix.

= 2.3.7 = 
* AJout des factures clients depuis le back-office.
* Affichages des factures clients sur la page compte du client.

= 2.3.8 =
* AJout du champs répéteur pour les factures
* Récupération des factures via une boucle 
* Ajout de la modification du titre de l'onglet 'Mes factures' depuis le back-office
* Personnalisationd de la présentation des factures client sous forme de tableau avec bouton de téléchargement et mignature PDF
* Ajout d'une visionneuse PDF directement sur l'onglet Mes tarifs

= 2.3.9 =
* desactivation de bouton d'ajout au panier et du selecteur de quantité quand l'utilisateur n'est pas connecté
* Traduction des en-tête de colonne de PVY (product variation table)

= 2.3.10 = 
* Réutilisation méthode de recherche par SKU précédent plugin.

= 2.4.0 =
* Ajustement CSS & JS dans style-admin.css pour supprimer les colonnes Prix et Ajout au panier de PVT lorsque l'utilisateur n'est pas connecté.
* Mise à jour de init.php pour initialiser les trad de PVT (quantité et mise au panier)
* Initialisation du fichier pvt-customization.php
* Ajout des fonctions Widget tableau de bord et nous consulter sur le back-office

= 2.4.1=
* Desactivation de l'option d'affichage de facture sur le compte utilisateur.