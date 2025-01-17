<?php
use WHMCS\View\Menu\Item as MenuItem;
add_hook('ClientAreaPrimarySidebar', 1, function(MenuItem $primarySidebar) {
    $service = Menu::context('service');
    $servertype = $service->product->servertype;
    if ($servertype!="cpanel"){
        return;
    }
     $domain = $service->domain;
    $username = $service->username;
    $cloudflare = 'http://'.$domain.'';
    $get_cf = get_headers($cloudflare, 1)[Server];
    if ($get_cf != 'cloudflare'){
        $cpanelhref = 'https://'.$domain.':2083/login/';
        $webmailhref = 'https://'.$domain.':2096/login/';
        $webwhmhref = 'https://'.$domain.':2087/login/';
        $command = 'DecryptPassword';
        $postData = array('password2' => $service->password);
        $results = localAPI($command, $postData);
        $password = $results['password'];
        if ($service->product->type=="reselleraccount"){
            $whmcpanel = '<input class="btn btn-primary btn-sm block-btn mb-1" type="submit" formaction="'.$webwhmhref.'" value="'.Lang::trans('cpanelwhmlogin').'"/>';
         }
        $bodyhtml = '<form method="post" action="'.$cpanelhref.'" target="_blank">
        <input type="hidden" name="user" value ="'.$username.'"/>
        <input type="hidden" name="pass" value ="'.$password.'"/>
        <input class="btn btn-success btn-sm block-btn mb-1" type="submit" value="'.Lang::trans('cpanellogin').'"/>
        <input class="btn btn-danger btn-sm block-btn mb-1" type="submit" formaction="'.$webmailhref.'" value="'.Lang::trans('cpanelwebmaillogin').'"/>'.$whmcpanel.''.'
        </form>
        <a href="/contact.php" class="btn btn-info btn-sm block-btn mb-1" data-toggle="tooltip" data-placement="bottom" title="Si tiene problemas para ingresar a su panel, puede deberse a que usa una CDN o proxy como CloudFlare en su dominio, en este caso, contacte con soporte.">Ayuda <i class="fas fa-question-circle"></i></a>
        <style>.block-btn {width: 100%;} .mb-1 {margin-bottom:5px;}</style>';
        if (!is_null($primarySidebar->getChild('Service Details Actions'))) {
                $primarySidebar->getChild('Service Details Actions')
                                ->removeChild('Login to cPanel')
                                ->removeChild('Login to Webmail')
                                ->removeChild('Login to WHM');
        }
                $primarySidebar->addChild('cPanel Login', array(
                                'label' => 'Ingresar a cPanel/WHM',
                                'icon' => 'fa-server',
                                'order' => 20,
                                'footerHtml' => $bodyhtml,
                                ));
    }
});
