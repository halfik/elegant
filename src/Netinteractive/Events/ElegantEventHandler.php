<?php
namespace Netinteractive\Events;

use \Netinteractive\Elegant\Elegant AS Elegant;

/**
 * Class ElegantEventHandler
 */
class ElegantEventHandler {


    /**
     * @param Elegant $user
     */
    public function readFilters(Elegant $user){
       echo 2; exit;
    }

    /**
     * Rejestrujemy eventy uzytkownika
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('eloquent.elegant.before.setAttribute', 'ElegantEventHandler@readFilters');
    }

}