<?php

/**
 * @author sinacek
 */

class Accountancy_BasePresenter extends BasePresenter {
    
    protected $service;
    /**
     * id volane v url, vetsinou id akce
     * @var int
     */
    protected $aid;

    protected function startup() {
        parent::startup();
        
        if(!$this->user->isLoggedIn()){
            $this->backlink = $this->storeRequest();
            //http://localhost/bakalarka/?presenter=Accountancy%3ADefault
            $this->redirect (":Default:", array("backlink"=>$this->backlink));
        }
        
        $sis = SkautIS::getInstance();
        if($sis->isLoggedIn())
            $sis->updateLogoutTime();
        
        if(($aid = $this->context->httpRequest->getQuery("aid"))) {
            $this->template->aid = $aid;
            $this->aid = $aid;
        }

//        $dataStorage = new Ucetnictvi_BaseStorage();
//        $this->categoriesIn = $dataStorage->getParagonCategoriesIn();
//        $this->categoriesOut = $dataStorage->getParagonCategoriesOut();
//        $this->oddily = $dataStorage->getOddily();
//
//        $this->template->registerHelper('oddily', 'UcetnictviHelpers::getNameOfOddily'); //v before render nefunguje pro action to pdf
    }

    function beforeRender() {
        parent::beforeRender();
        $this->template->registerHelper('priceToString', 'AccountancyHelpers::priceToString');
//        $this->template->registerHelper('datNar', 'AccountancyHelpers::datNar');
        //$this->template->registerHelper('pCat', 'AccountancyHelpers::pCat');
        //dump($this->model->vyprava->getId());
    }

//    public function getCategories() {
//        return array_merge($this->categoriesIn, $this->categoriesOut);
//    }
//
//    /**
//     * @param string $d
//     * @return timestamp
//     * @deprecated
//     */
//    static function dateToTime($d) {
//        $a = explode("-", $d);
//        return mktime(0, 0, 0, $a[1], $a[2], $a[0]);
//    }
// 
//    /**
//     * ulozi akci
//     */
//    function handleSave() {
//        $this->saveAkce();
//        $this->redirect("this");
//    }
//
//    /**
//     * resetuje akci na defaultni nastaveni
//     */
//    function handleClear() {
//        $this->service->clear();
//        $this->flashMessage("Neuložené informace byly smazány.");
//        $this->redirect('default');
//    }
//
//    /**
//     * ulozi aktualni akci a odemkne zamek
//     */
//    function handleSaveUnlock() {
//        $this->saveAkce();
//        $this->service->unlock();
//        $this->service->clear();
//        $this->redirect("default");
//    }
//
//    protected function saveAkce($isFm = true) {
//        $ret = $this->service->save();
//        $fmstatus = $fm = "";
//        if ($isFm) {
//            switch ($ret) {
//                case "insert":
//                    $fm = "Výprava byla úspěšně uložena.";
//                    break;
//                case "update":
//                    $fm = "Výprava byla úspěšně upravena.";
//                    break;
//                case "noinsert":
//                    $fm = "Výpravu se nepodařilo uložit";
//                    $fmstatus = "fail";
//                    break;
//                case "noupdate":
//                    $fm = "Výprava nebyla změněna";
//                    break;
//                case "noaccess":
//                    $fm = "Nemáte právo upravovat tento záznam";
//                    $fmstatus = "fail";
//                    break;
//            }
//        }
//        $this->flashMessage($fm, $fmstatus);
//    }

    //vrati routy pro modull
    static function createRoutes($router, $prefix ="") {

        $router[] = new Route($prefix . 'Ucetnictvi/p-<presenter>/a-<action>/', array(
                    'module' => "Accountancy",
//                    'presenter' => 'Default',
//                    'action' => 'default',
                ));
    }

}
