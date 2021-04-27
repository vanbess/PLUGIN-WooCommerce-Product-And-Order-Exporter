<?php
/**
 * Queries order state, filters and fixes it as required and returns fixed version
 *
 * @param object $order
 * @return void
 */
function sbwc_get_full_order_state($order)
{

    $country = $order->get_shipping_country();
    $state = $order->get_shipping_state();
    $city = $order->get_shipping_city();

    $countries_obj = new WC_Countries();
    $countries_array = $countries_obj->get_countries();
    $country_states_array = $countries_obj->get_states();

    // Get the state name:
    if ($country != "US") {
        $state_name = $country_states_array[$country][$state];

        $gr_states = array(
            'I' => __('Attica', 'woocommerce'),
            'A' => __('Eastern Macedonia and Thrace', 'woocommerce'),
            'B' => __('Central Macedonia', 'woocommerce'),
            'C' => __('Western Macedonia', 'woocommerce'),
            'D' => __('Epirus', 'woocommerce'),
            'E' => __('Thessaly', 'woocommerce'),
            'F' => __('Ionian Islands', 'woocommerce'),
            'G' => __('Western Greece', 'woocommerce'),
            'H' => __('Central Greece', 'woocommerce'),
            'J' => __('Peloponnese', 'woocommerce'),
            'K' => __('North Aegean', 'woocommerce'),
            'L' => __('South Aegean', 'woocommerce'),
            'M' => __('Crete', 'woocommerce'),
        );

        $jap_states = array(
            'JP01' => '北海道',
            'JP02' => '青森県',
            'JP03' => '岩手県',
            'JP04' => '宮城県',
            'JP05' => '秋田県',
            'JP06' => '山形県',
            'JP07' => '福島県',
            'JP08' => '茨城県',
            'JP09' => '栃木県',
            'JP10' => '群馬県',
            'JP11' => '埼玉県',
            'JP12' => '千葉県',
            'JP13' => '東京都',
            'JP14' => '神奈川県',
            'JP15' => '新潟県',
            'JP16' => '富山県',
            'JP17' => '石川県',
            'JP18' => '福井県',
            'JP19' => '山梨県',
            'JP20' => '長野県',
            'JP21' => '岐阜県',
            'JP22' => '静岡県',
            'JP23' => '愛知県',
            'JP24' => '三重県',
            'JP25' => '滋賀県',
            'JP26' => '京都府',
            'JP27' => '大阪府',
            'JP28' => '兵庫県',
            'JP29' => '奈良県',
            'JP30' => '和歌山県',
            'JP31' => '鳥取県',
            'JP32' => '島根県',
            'JP33' => '岡山県',
            'JP34' => '広島県',
            'JP35' => '山口県',
            'JP36' => '徳島県',
            'JP37' => '香川県',
            'JP38' => '愛媛県',
            'JP39' => '高知県',
            'JP40' => '福岡県',
            'JP41' => '佐賀県',
            'JP42' => '長崎県',
            'JP43' => '熊本県',
            'JP44' => '大分県',
            'JP45' => '宮崎県',
            'JP46' => '鹿児島県',
            'JP47' => '沖縄県'
        );

        if ($country == "JP") {
            $state_name = $jap_states[$state];
        } elseif ($country == "GR") {
            $state_name = $gr_states[$state];
        }

        if (!$state_name) {
            if ($state) {
                $state_name = $state;
            } else {
                $state_name = $city;
            }
        }

        if ($state_name == "Zürich") {
            $state_name = "Zurich";
        }
    } else {
        $state_name = $state;
    }

    return trim($state_name);
}
