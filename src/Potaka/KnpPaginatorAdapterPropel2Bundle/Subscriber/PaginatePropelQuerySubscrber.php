<?php

namespace Potaka\KnpPaginatorAdapterPropel2Bundle\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;

use Propel\Runtime\ActiveQuery\ModelCriteria;

/**
 * Description of PaginatePropelQuerySubscrber
 *
 * @author po_taka <angel.koilov@gmail.com>
 */
class PaginatePropelQuerySubscrber implements EventSubscriberInterface
{
    public function items(ItemsEvent $event)
    {
        $target = $event->target;
        if ($target instanceof ModelCriteria) {
            $sortFieldParamName = $event->options['sortFieldParameterName'];
            if (isset($_GET[$sortFieldParamName])) {
                $direction = strtolower($_GET[$event->options['sortDirectionParameterName']]) === 'asc' ? 'asc' : 'desc';
                $part = $_GET[$sortFieldParamName];

                if (isset($event->options['sortFieldWhitelist'])) {
                    if (!in_array($_GET[$sortFieldParamName], $event->options['sortFieldWhitelist'])) {
                        throw new \UnexpectedValueException("Cannot sort by: [{$_GET[$sortFieldParamName]}] this field is not in whitelist");
                    }
                }
                
                $target->orderBy($part, $direction);
            }
            
            $event->count = $target->count();
            $limit = $event->getLimit();
            $offset = $event->getOffset();
            $target->limit($limit);
            $target->offset($offset);
            $items = $target->find();
            $itemsData = $items->getData();
            $event->items = $itemsData;
            $event->stopPropagation();
        }
    }
    
    public static function getSubscribedEvents()
    {
        return[
            'knp_pager.items' => [
                'items',
                /*increased priority to override any internal*/
                1,
            ],
        ];
    }
}
