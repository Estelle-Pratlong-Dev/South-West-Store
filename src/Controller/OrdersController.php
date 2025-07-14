<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Orders;
use Doctrine\ORM\EntityManagerInterface;

class OrdersController extends AbstractController
{

    /************** Tableau de marques ****************/
    public $brands = [

        1 => 'ADIDAS',
        2 => 'CONVERSE',
        3 => 'JORDAN',
        4 => 'NIKE',
        5 => 'REEBOOK',
        6 => 'YEEZY',

    ];

    /***************************************************************
                        FONCTION ORDERS USER
     ***************************************************************/

    public function userOrders()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $ordersFound = $em->getRepository(Orders::class)->findBy(['user_id' => $user->getId()]);


        return $this->render('orders/user_orders.html.twig', [
            'user' => $user,
            'orders' => $ordersFound ?? '',
            'brand'  => $this->brands ?? '',
        ]);
    }

    /***************************************************************
                        FONCTION ORDERS ADMIN
     ***************************************************************/

    public function adminOrders()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $ordersFound = $em->getRepository(Orders::class)->findAll();


        return $this->render('orders/admin_orders.html.twig', [
            'user' => $user,
            'orders' => $ordersFound ?? '',
            'brand'  => $this->brands ?? '',
        ]);
    }
}
