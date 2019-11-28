<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Product;
use App\Entity\ProductOrder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function order(Request $request, \Swift_Mailer $mailer)
    {
        $name = $request->request->get("name");
        $email = $request->request->get("email");
        $data = $request->request->get("products");
        $productsArray = json_decode($data, true);
        if (!$name || !$email || !$productsArray) {
            $response = new Response(
                "not all data presented",
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'text/html']
            );
            return $response;
        }

        $order = new Book();
        $order->setName($name)->setEmail($email);
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository(Product::class)->findById(array_map(function ($products) {
            return $products['id'];
        }, $productsArray));

        $price = 0;
        for ($i = 0; $i < count($productsArray); $i++) {
            $productOrder = new ProductOrder();
            $quantity = $productsArray[$i]['quantity'];
            $product = array_filter($products, function ($product) use ($productsArray, $i) {
                return $product->getId() == $productsArray[$i]['id'];
            });
            /* @var Product $product*/
            $product = array_pop($product);

            $productSizes = $product->getSizes();
            $size = array_filter($productSizes, function ($size) use ($productsArray, $i) {
                return $size->getId() == $productsArray[$i]['size'];
            });

            $size = array_pop($size);
            $productOrder->setProduct($product)->setQuantity($quantity)->setSize($size);
            $order->addProductOrder($productOrder);
            $price += ($product->getPrice() + $size->getAddPrice()) * $quantity;
            $em->persist($productOrder);
        }

        $order->setTotalPrice($price);
        $em->persist($order);
        $em->flush();
        $response = new Response(
            "success",
            Response::HTTP_OK,
            ['content-type' => 'text/javascript']
        );
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo($email)
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'emails/order.html.twig',
                    ['order' => $order]
                ),
                'text/html'
            );
        $mailer->send($message);
        if ($request->request->get("fromCart")) {
            $products =[];
            $response->headers->setCookie(Cookie::create("products", json_encode($products)));
            $response->sendHeaders();
            return $this->redirectToRoute("cart");
        }
        return $this->redirectToRoute("pdp", ["product" => $product->getId()]);
    }

    /**
     * @Route("/orderForm", name="order_form")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderForm(Request $request)
    {
        $products = $request->cookies->get("products");
        $isFromCart = false;
        return $this->render("order/index.html.twig", [
            "products" => $products, "isFromCart" => $isFromCart
        ]);
    }
}
