<?php
namespace Netinteractive\Elegant\Events;

use Netinteractive\Elegant\Elegant;

/**
 * Class ElegantEventHandler
 */
class ElegantEventHandler {

    /**
     * @param Elegant $model
     */
    public function readFilters(Elegant $model){
       echo 2; exit;
    }

    /**
     * Rejestrujemy eventy uzytkownika
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {   echo 149; exit;
        $events->listen('eloquent.elegant.before.setAttribute', 'ElegantEventHandler@readFilters');
    }

}