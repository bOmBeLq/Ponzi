<?php

namespace Bml\AppBundle\Controller;

use Bml\AppBundle\Entity\Account;
use Bml\AppBundle\Entity\AccountRepository;
use Bml\AppBundle\Entity\Deposit;
use Bml\AppBundle\Entity\PayoutRepository;
use Bml\AppBundle\Entity\Round;
use Bml\AppBundle\Entity\RoundRepository;
use Bml\CoinBundle\Manager\CoinManager;
use Bml\CoinBundle\Manager\CoinManagerContainer;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\Controller
 * @Route("/", service="bml.default.controller")
 */
class DefaultController extends Controller
{
    /**
     * @var CoinManager
     */
    private $manager;

    /**
     * @var AccountRepository
     */
    private $accountRepo;

    /**
     * @var RoundRepository
     */
    private $roundRepo;
    /**
     * @var PayoutRepository
     */
    private $payoutRepo;

    /**
     * @param CoinManagerContainer $coinManagerContainer
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Bml\AppBundle\Entity\RoundRepository $roundRepo
     * @param \Bml\AppBundle\Entity\PayoutRepository $payoutRepo
     * @param AccountRepository $accountRepo
     * @param $accountPrefix
     */
    public function __construct(CoinManagerContainer $coinManagerContainer, EntityManager $em, RoundRepository $roundRepo,
                                PayoutRepository $payoutRepo, AccountRepository $accountRepo, $accountPrefix)
    {
        $this->manager = $coinManagerContainer->get('main');
        $this->accountRepo = $accountRepo;
        $this->roundRepo = $roundRepo;
        $this->payoutRepo = $payoutRepo;
    }


    /**
     * @Route("/{round}", name="index", requirements={"round": "\d+"})
     * @Template()
     */
    public function indexAction(Request $request, $round = null)
    {
        $round = $round ? $this->roundRepo->find($round) : $this->roundRepo->findCurrent();
        $response = $this->render('AppBundle:Default:index.html.twig', $this->common([
            'page' => 'index',
            'round' => $round
        ]));

        if ($ref = $request->get('ref')) {
            $this->getSession()->set('ref', $ref);
            $response->headers->setCookie(new Cookie('ref', $ref, time() + 60 * 60 * 24 * 30, '/', null, false, false));
        }

        return $response;
    }

    /**
     * @Route("/{round}/stats", name="stats", requirements={"round": "\d+"})
     * @Template()
     */
    public function statsAction($round = null)
    {
        $round = $round ? $this->roundRepo->find($round) : $this->roundRepo->findCurrent();
        $payouts = $this->payoutRepo->findBy(['round' => $round], ['id' => 'desc']);
        return $this->common([
            'page' => 'stats',
            'payouts' => $payouts,
            'round' => $round
        ]);
    }

    /**
     * @Route("/deposit-details/{id}", name="deposit-details")
     * @Template()
     */
    public function depositDetailsAction(Deposit $deposit)
    {
        $round = $deposit->getRound();
        return $this->common([
            'page' => 'deposit-details',
            'deposit' => $deposit,
            'round' => $round
        ]);
    }

    /**
     * @Route("/{round}/register", name="register", requirements={"round": "\d+"})
     * @Template()
     */
    public function registerAction(Request $request, $round = null)
    {
        $round = $round ? $this->roundRepo->find($round) : $this->roundRepo->findCurrent();
        $address = $request->get('address', null);
        if (!$address) {
            $this->getSession()->getFlashBag()->set(
                'error',
                'Address is required'
            );
        } else {
            $validation = $this->manager->validateAddress($address);
            if (!$validation->isValid()) {
                $this->getSession()->getFlashBag()->set(
                    'error',
                    'Invalid address'
                );
            } else {
                if (!$account = $this->accountRepo->findOneBy(['withdrawAddress' => $address])) {
                    $depositAddress = $this->manager->getNewAddress($round->getWalletAccount());
                    $account = new Account();
                    $account->setWithdrawAddress($address);
                    $account->setDepositAddress($depositAddress);
                    if (($ref = $this->getSession()->get('ref')) || ($ref = $request->cookies->get('ref'))) {
                        if ($refAccount = $this->accountRepo->find($ref)) {
                            $account->setReferrer($refAccount);
                            $refAccount->addReferralRegistersCount();
                        }
                    }
                    $this->getDoctrine()->getManager()->persist($account);
                    $this->getDoctrine()->getManager()->flush();
                }
                $this->getSession()->set('account', $account->getId());
            }
        }
        return $this->redirect($this->generateUrl('stats', ['round' => $round->getId()]));
    }

    /**
     * @Route("/{round}/transactions", name="wallet_transactions", requirements={"round": "\d+"})
     * @Template()
     */
    public function walletTransactionsListAction($round = null)
    {
        $round = $round ? $this->roundRepo->find($round) : $this->roundRepo->findCurrent();
        $result = $this -> manager->rawRequest('listtransactions', [$round->getWalletAccount(), 999999]);
        // we need to decode and encode to get pretty formated result
        $result = json_encode(json_decode($result), JSON_PRETTY_PRINT);
        return new Response('<pre>'.$result.'</pre>');
    }
    /**
     * @param array $params
     * @return array
     */
    private function common($params)
    {
        $round = $params['round'];
        if(!$round) {
            throw $this->createNotFoundException('No round started.');
        }
        /* @var $round Round */

        $params['stats'] = $round->getStats();
        if ($account = $this->getSession()->get('account')) {
            if ($account = $this->accountRepo->find($account)) {
                $params['account'] = $account;
            }
        }
        return $params;
    }

    /**
     * @return Session
     */
    private function getSession()
    {
        return $this->container->get('session');
    }


    /**
     * @Route("/reset", name="reset")
     * @Template()
     */
    public function resetAction()
    {
        $round =  $this->roundRepo->findCurrent();
        $this->getSession()->clear();
        return $this->redirect($this->generateUrl('stats', ['round' => $round->getId()]));
    }
}
