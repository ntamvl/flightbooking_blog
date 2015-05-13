<?php
function momizat_weather ($city, $units = 'metric', $date_format = 'm/d/Y',  $lang = 'en', $display='', $days = 6) {
if (isset($_POST['nonce'])) {
$nonce = $_POST['nonce'];
if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) )
die ( 'Nope!' );
}

if (isset($_POST['city'])) {
    $city = $_POST['city'];
}

if (isset($_POST['units'])) {
    $units = $_POST['units'];
}
if (isset($_POST['lang'])) {
    $lang = $_POST['lang'];
}
  $today_weather_data = get_transient('mom_weather_data_'.$city);
// Today Weather
if ($today_weather_data == false) {
$today_weather_url = wp_remote_get('http://api.openweathermap.org/data/2.5/weather?q='.$city.'&units='.$units.'&lang='.$lang);
if (is_wp_error($today_weather_url)) return false;

$today_weather_data = json_decode( $today_weather_url['body'], true);
set_transient('mom_weather_data_'.$city, $today_weather_data, 3600 );
}

$code = $today_weather_data['cod'];
if ($code != 404) { 

$today_temp = (int)$today_weather_data['main']['temp'];
$today_hum = (int)$today_weather_data['main']['humidity'];
$today_pres = (int)$today_weather_data['main']['pressure'];
$today_wind = $today_weather_data['wind']['speed'];
$today_icon = $today_weather_data['weather'][0]['icon'];

$this_day = date($date_format);
$today_date =  date("m/d/Y");
$site_time = mom_option('main_time') != '' ? mom_option('main_time') : '+2';


$x = '';
if ($units == 'metric') {
    $x = '&#8451;';
} elseif ($units == 'imperial') {
    $x = '&#8457;';
}
$today_icon_class = '';
$base_cloud = '<i class="basecloud"></i>';
switch ($today_icon) {
    case '01d':
        $today_icon_class = 'icon-sun';
        $base_cloud = '';
    break;

    case '01n':
        $today_icon_class = 'icon-moon';
        $base_cloud = '';
    break;
    case '02d':
        $today_icon_class = 'icon-sunny';
        $base_cloud = '<i class="icon-cloud"></i>';
    break;

    case '02n':
        $today_icon_class = 'icon-night';
        $base_cloud = '<i class="icon-cloud"></i>';
    break;
    case '03d':
        $today_icon_class = 'icon-cloud';
        $base_cloud = '';
    break;

    case '03n':
        $today_icon_class = 'icon-cloud';
        $base_cloud = '';
    break;
    case '04d':
        $today_icon_class = 'icon-cloud';
        $base_cloud = '<i class="icon-cloud behind"></i>';
    break;

    case '04n':
        $today_icon_class = 'icon-cloud';
        $base_cloud = '<i class="icon-cloud behind"></i>';
    break;
    case '09d':
        $today_icon_class = 'icon-drizzle icon-sunny';
    break;

    case '09n':
        $today_icon_class = 'icon-drizzle icon-night';
    break;

    case '10d':
        $today_icon_class = 'icon-rainy icon-sunny';
    break;

    case '10n':
        $today_icon_class = 'icon-rainy icon-night';
    break;

    case '11d':
        $today_icon_class = 'icon-thunder icon-sunny';
    break;

    case '11n':
        $today_icon_class = 'icon-thunder icon-night';
    break;

    case '13d':
        $today_icon_class = 'icon-snowy icon-sunny';
    break;

    case '13n':
        $today_icon_class = 'icon-snowy icon-night';
    break;
    
    case '50d':
        $today_icon_class = 'icon-mist';
        $base_cloud = '';
    break;

    case '50n':
        $today_icon_class = 'icon-mist';
        $base_cloud = '';
    break;
}
  $daily_weather_data = get_transient('mom_daily_weather_data_'.$city);
if ($daily_weather_data == false) {
$daily_weather_url = wp_remote_get('http://api.openweathermap.org/data/2.5/forecast/daily?q='.$city.'&mode=json&units='.$units.'&lang='.$lang.'&cnt='.($days+1));
if (is_wp_error($daily_weather_url)) { return false;} 
$daily_weather_data = json_decode($daily_weather_url['body'], true);
set_transient('mom_daily_weather_data_'.$city, $daily_weather_data, 10800);
}
if ($display == '') {
$city_name = $city;
} else {
    $city_name = $display;
}

switch (date('M')) {
    case 'Jan':
        $tm_print = __('Jan', 'framework');
    break;
    case 'Feb':
        $tm_print = __('Feb', 'framework');
    break;
    case 'Mar':
        $tm_print = __('Mar', 'framework');
    break;
    case 'Apr':
        $tm_print = __('Apr', 'framework');
    break;
    case 'May':
        $tm_print = __('May', 'framework');
    break;
    case 'Jun':
        $tm_print = __('Jun', 'framework');
    break;
    case 'Jul':
        $tm_print = __('Jul', 'framework');
    break;
    case 'Aug':
        $tm_print = __('Aug', 'framework');
    break;
    case 'Sep':
        $tm_print = __('Sep', 'framework');
    break;
    case 'Oct':
        $tm_print = __('Oct', 'framework');
    break;
    case 'Nov':
        $tm_print = __('Nov', 'framework');
    break;
    case 'Dec':
        $tm_print = __('Dec', 'framework');
    break;
}
?>
                    <div class="weather-widget">

                               <div class="current-day-weather">
                                <div class="cdw-data border-box">
                                    <h3 class="city_name"><?php echo $city_name; ?></h3>
                                    <div class="cdw-meta"><span class="cd-date"><?php echo $tm_print; echo date('d'); ?></span> <span class="cs-time"><?php echo date("h:i", strtotime($site_time.' hours', time())); ?></span></div>
                                    <ol>
                                        <li><?php _e('Humidity', 'theme'); ?> <span><?php echo $today_hum; ?>%</span></li>
                                        <li><?php _e('Pressure', 'theme'); ?> <span><?php echo $today_pres; ?></span></li>
                                        <li><?php _e('Winds', 'theme'); ?> <span><?php echo $today_wind; ?>mph</span></li>
                                    </ol>
                                </div> <!--data-->
                                
                                <div class="cd-temp border-box">
                                    <span><?php _e('now', 'theme'); ?></span>
                                    <div class="weather-icon cd-icon">
                                            <div class="w-icon">
                                                <?php echo $base_cloud; ?>
                                                <i class="<?php echo $today_icon_class; ?>"></i>
                                            </div>
                                            <div class="clearfix"></div>
                                    </div>
                                    <span class="current-temp"><?php echo $today_temp.$x; ?></span>
                                </div>
                            </div> <!--current day-->
                            <div class="next-days">
                                <ol>
                                <?php
                                $count = 0;
                                $i = 1;
                                foreach ( $daily_weather_data['list'] as $day ) {
                                    if($count++ < 1) continue ;

$today = new DateTime($today_date);
$date = $today->add(new DateInterval('P'.$i.'D'));

                                    $desc = $day['weather'][0]['description']; 
                                    $icon = $day['weather'][0]['icon'];
                                    $humidity = $day['humidity'];
                                    $wind = $day['speed'];
                                    $min_temp = (int)$day['temp']['min'];
                                    $max_temp = (int)$day['temp']['max'];
                                    $pressure = (int)$day['pressure'];
                                    $base_cloud = '<i class="basecloud"></i>';
switch ($icon) {
    case '01d':
        $icon_class = 'icon-sun';
        $base_cloud = '';
    break;

    case '01n':
        $icon_class = 'icon-moon';
        $base_cloud = '';
    break;
    case '02d':
        $icon_class = 'icon-sunny';
        $base_cloud = '<i class="icon-cloud"></i>';
    break;

    case '02n':
        $icon_class = 'icon-night';
        $base_cloud = '<i class="icon-cloud"></i>';
    break;
    case '03d':
        $icon_class = 'icon-cloud';
        $base_cloud = '';
    break;

    case '03n':
        $icon_class = 'icon-cloud';
        $base_cloud = '';
    break;
    case '04d':
        $icon_class = 'icon-cloud';
        $base_cloud = '<i class="icon-cloud behind"></i>';
    break;

    case '04n':
        $icon_class = 'icon-cloud';
        $base_cloud = '<i class="icon-cloud behind"></i>';
    break;
    case '09d':
        $icon_class = 'icon-drizzle icon-sunny';
    break;

    case '09n':
        $icon_class = 'icon-drizzle icon-night';
    break;

    case '10d':
        $icon_class = 'icon-rainy icon-sunny';
    break;

    case '10n':
        $icon_class = 'icon-rainy icon-night';
    break;

    case '11d':
        $icon_class = 'icon-thunder icon-sunny';
    break;

    case '11n':
        $icon_class = 'icon-thunder icon-night';
    break;

    case '13d':
        $icon_class = 'icon-snowy icon-sunny';
    break;

    case '13n':
        $icon_class = 'icon-snowy icon-night';
    break;
    
    case '50d':
        $icon_class = 'icon-mist';
        $base_cloud = '';
    break;

    case '50n':
        $icon_class = 'icon-mist';
        $base_cloud = '';
    break;
}
switch ($date->format('D')) {
    case 'Fri':
        $d_print = __('Fri', 'framework');
    break;
    case 'Sat':
        $d_print = __('Sat', 'framework');
    break;
    case 'Sun':
        $d_print = __('Sun', 'framework');
    break;
    case 'Mon':
        $d_print = __('Mon', 'framework');
    break;
    case 'Tue':
        $d_print = __('Tue', 'framework');
    break;
    case 'Wed':
        $d_print = __('Wed', 'framework');
    break;
    case 'Thu':
        $d_print = __('Thu', 'framework');
    break;
}
switch ($date->format('M')) {
    case 'Jan':
        $m_print = __('Jan', 'framework');
    break;
    case 'Feb':
        $m_print = __('Feb', 'framework');
    break;
    case 'Mar':
        $m_print = __('Mar', 'framework');
    break;
    case 'Apr':
        $m_print = __('Apr', 'framework');
    break;
    case 'May':
        $m_print = __('May', 'framework');
    break;
    case 'Jun':
        $m_print = __('Jun', 'framework');
    break;
    case 'Jul':
        $m_print = __('Jul', 'framework');
    break;
    case 'Aug':
        $m_print = __('Aug', 'framework');
    break;
    case 'Sep':
        $m_print = __('Sep', 'framework');
    break;
    case 'Oct':
        $m_print = __('Oct', 'framework');
    break;
    case 'Nov':
        $m_print = __('Nov', 'framework');
    break;
    case 'Dec':
        $m_print = __('Dec', 'framework');
    break;
}
?>
                                     <li>
                                        <div class="day-summary">
                                            <div class="d-date">
                                                <span class="dn"><?php echo $d_print; ?></span>
                                                <span class="dd"><?php echo $m_print; ?><?php echo $date->format('d'); ?></span>
                                            </div>
                                            <div class="d-decs">
                                                <span><?php echo $desc; ?></span>
                                                <div class="weather-icon d-icon">
                                                <?php echo $base_cloud; ?>
                                                <i class="<?php echo $icon_class; ?>"></i>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </div>
                                            <span class="d-handle"><i class="fa-icon-caret-down"></i></span>
                                        </div>
                       
                                        <div class="day-details">
                                            <ol>
                                                <li><span><?php _e('HI/LO:', 'theme'); ?></span>  <?php echo $max_temp; ?>/<?php echo $min_temp.$x; ?></li>
                                                <li><span><?php _e('Humidity:', 'theme'); ?></span>  <?php echo $humidity; ?></li>
                                                <li><span><?php _e('Pressure:', 'theme'); ?></span> <?php echo $pressure; ?></li>
                                                <li><span><?php _e('Winds:', 'theme'); ?></span> <?php echo $wind; ?></li>
                                            </ol>
                                        </div>
                                        <div class="clear"></div>
                                    </li>

                                <?php $i++; } ?>
                                </ol>                                
                            </div>
                            </div>
    
<?php
} //cod not = 404
if (isset($_POST['city'])) {
    exit();
}

}