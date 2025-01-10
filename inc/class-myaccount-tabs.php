<?php
namespace CreactiveWeb;

if (!defined('ABSPATH')) exit;

class MyAccountTabs {
    public static function init() {
        add_filter('woocommerce_account_menu_items', [ __CLASS__, 'ajouterMenuMonCompte' ]);
        add_action('init', [ __CLASS__, 'ajouterEndpoints' ]);

        // On attache l’affichage du contenu à l’endpoint
        $options = get_option('creactive_settings');
        $tab1 = $options['my_account_tab_1_slug'] ?? 'mes-tarifs';
        $tab2 = $options['my_account_tab_2_slug'] ?? 'mes-demandes-de-devis';
        $tab3 = $options['my_account_tab_3_slug'] ?? 'mes-factures';

        add_action("woocommerce_account_{$tab1}_endpoint", [ __CLASS__, 'contenuTarifs' ]);
        add_action("woocommerce_account_{$tab2}_endpoint", [ __CLASS__, 'contenuDevis' ]);
        add_action("woocommerce_account_{$tab3}_endpoint", [ __CLASS__, 'contenuFactures' ]);
    }

    public static function ajouterMenuMonCompte($items) {
        $options = get_option('creactive_settings');
        $tab1_slug  = $options['my_account_tab_1_slug']  ?? 'mes-tarifs';
        $tab1_label = $options['my_account_tab_1_label'] ?? 'Mes Tarifs';
        $tab2_slug  = $options['my_account_tab_2_slug']  ?? 'mes-demandes-de-devis';
        $tab2_label = $options['my_account_tab_2_label'] ?? 'Demande de devis';
        $tab3_slug  = $options['my_account_tab_3_slug']  ?? 'mes-factures';
        $tab3_label = $options['my_account_tab_3_label'] ?? 'Mes Factures';

        $nouveaux = [
            $tab1_slug => $tab1_label,
            $tab2_slug => $tab2_label,
            $tab3_slug => $tab3_label
        ];

        // Insérer juste après "Dashboard"
        $items = array_slice($items, 0, 1, true)
               + $nouveaux
               + array_slice($items, 1, null, true);

        // On peut enlever "Téléchargements" par exemple
        if (isset($items['downloads'])) unset($items['downloads']);

        return $items;
    }

    public static function ajouterEndpoints() {
        $options = get_option('creactive_settings');
        $tab1_slug = $options['my_account_tab_1_slug'] ?? 'mes-tarifs';
        $tab2_slug = $options['my_account_tab_2_slug'] ?? 'mes-demandes-de-devis';
        $tab3_slug = $options['my_account_tab_3_slug'] ?? 'mes-factures';

        add_rewrite_endpoint($tab1_slug, EP_ROOT | EP_PAGES);
        add_rewrite_endpoint($tab2_slug, EP_ROOT | EP_PAGES);
        add_rewrite_endpoint($tab3_slug, EP_ROOT | EP_PAGES);
    }

    public static function contenuTarifs() {
        $user_id = get_current_user_id();
        // ex. ACF get_field
        $tarif_url = get_field('tarifs_preferentiels', 'user_'.$user_id);

        echo '<h3>Mes Tarifs Préférentiels</h3>';
        if ($tarif_url) {
            echo '<p>Vous pouvez télécharger vos tarifs ici :</p>';
            echo '<a class="button" href="' . esc_url($tarif_url) . '">Télécharger</a>';
        } else {
            echo '<p>Aucun tarif n’est disponible.</p>';
        }
    }

    public static function contenuDevis() {
        $options = get_option('creactive_settings');
        $form_id = $options['my_account_tab_2_form_id'] ?? '';

        echo '<h3>Mes Demandes de Devis</h3>';
        if ($form_id) {
            echo do_shortcode('[forminator_form id="' . intval($form_id) . '"]');
        } else {
            echo '<p>Aucun formulaire n’est configuré.</p>';
        }
    }

    public static function contenuFactures() {
        $user_id = get_current_user_id();
        // Récupérer le répéteur "factures"
        $factures = get_field('factures', 'user_' . $user_id);
    
        echo '<h3>Mes Factures</h3>';
    
        if ($factures && is_array($factures)) {
    
            // On affiche un mini CSS inline pour styliser le tableau
            echo '<style>
                table.mes-factures-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                table.mes-factures-table th, table.mes-factures-table td {
                    border: 1px solid #ddd;
                    padding: 10px;
                    text-align: left;
                    vertical-align: middle;
                }
                table.mes-factures-table th {
                    background: #f9f9f9;
                }
                .facture-icone {
                    width: 32px;
                    height: 32px;
                    margin-right: 8px;
                    vertical-align: middle;
                }
                .facture-pdf-icon {
                    width: 24px;
                    height: auto;
                    margin-right: 5px;
                    vertical-align: middle;
                }
            </style>';
    
            // Petite icône PDF (exemple), stockée quelque part dans le plugin ou en ligne
            // Tu peux la remplacer par l’URL de ton image ou icône
            $pdf_icon_url = CREACTIVEWEB_PLUGIN_URL . 'inc/pdf-icon.png'; 
            // (à toi de mettre l'image "pdf-icon.png" dans ton dossier inc/)
    
            echo '<table class="mes-factures-table">';
            echo '<thead>
                    <tr>
                        <th>Numéro de commande</th>
                        <th>Facture</th>
                    </tr>
                  </thead>';
            echo '<tbody>';
    
            foreach ($factures as $facture_item) {
                // Sous-champ "numero_de_commande" (texte)
                $numero_de_commande = !empty($facture_item['numero_de_commande'])
                    ? $facture_item['numero_de_commande']
                    : 'Sans numéro';
                
                // Sous-champ "facture" (fichier)
                $facture_file = !empty($facture_item['facture']) ? $facture_item['facture'] : null;
                $facture_url  = (is_array($facture_file) && !empty($facture_file['url'])) ? $facture_file['url'] : '';
    
                echo '<tr>';
    
                // 1) Colonne du numéro de commande
                echo '<td>' . esc_html($numero_de_commande) . '</td>';
    
                // 2) Colonne du lien de téléchargement
                echo '<td>';
                if ($facture_url) {
                    // Petit logo PDF (ou “miniature” si on veut)
                    echo '<a href="' . esc_url($facture_url) . '" target="_blank" style="text-decoration: none;">';
                    echo '<img class="facture-pdf-icon" src="' . esc_url($pdf_icon_url) . '" alt="PDF">';
                    echo 'Télécharger la facture';
                    echo '</a>';
                } else {
                    echo '<em>Aucun fichier disponible.</em>';
                }
                echo '</td>';
    
                echo '</tr>';
            }
    
            echo '</tbody></table>';
    
        } else {
            echo '<p>Aucune facture n’est disponible.</p>';
        }
    }
      
    
}