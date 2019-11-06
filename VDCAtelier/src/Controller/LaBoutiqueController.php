<?php

namespace Controller;

class laBoutiqueController extends Controller {


    public function mentionslegales() {
        $params['id'] = 'laboutique';
        return $this->render('layout.html', 'mentions_legales.html', $params);
    }

    public function conditionsGeneralesVentes() {
        $params['id'] = 'laboutique';
        return $this->render('layout.html', 'cgv.html', $params);

    }

}