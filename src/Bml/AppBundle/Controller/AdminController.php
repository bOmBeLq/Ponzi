<?php

namespace Bml\AppBundle\Controller;

use Bml\AppBundle\Entity\Round;
use Bml\AppBundle\Entity\RoundRepository;
use Bml\AppBundle\Form\LoginType;
use Bml\AppBundle\Form\RoundType;
use Bml\AppBundle\Manager\RoundManager;
use Bml\CoinBundle\Manager\CoinManager;
use Bml\CoinBundle\Manager\CoinManagerContainer;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\Controller
 * @Route("/admin", service="bml.admin.controller")
 */
class AdminController extends Controller
{
    /**
     * @var CoinManager
     */
    private $manager;

    /**
     * @var RoundRepository
     */
    private $roundRepo;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var BCryptPasswordEncoder
     */
    private $encoder;
    /**
     * @var \Bml\AppBundle\Manager\RoundManager
     */
    private $roundManager;

    /**
     * @param \Bml\CoinBundle\Manager\CoinManagerContainer $coinManagerContainer
     * @param RoundRepository $roundRepo
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Bml\AppBundle\Manager\RoundManager $roundManager
     */
    public function __construct(CoinManagerContainer $coinManagerContainer, RoundRepository $roundRepo, EntityManager $em,
                                RoundManager $roundManager)
    {
        $this->manager = $coinManagerContainer->get('main');
        $this->roundRepo = $roundRepo;
        $this->em = $em;
        $this->encoder = new BCryptPasswordEncoder(15);
        $this->roundManager = $roundManager;
    }

    /**
     * @Route("/", name="admin")
     * @Template()
     */
    public function adminAction(Request $request)
    {
        $this->checkIp($request);

        if (!$currentRound = $this->roundRepo->findOneBy(['started' => true, 'finished' => false])) {
            return $this->redirect($this->generateUrl('admin_round'));
        }
        if ($this->manager->getInfo()->getUnlockedUntil() === null) {
            $walletStatus = 'Not encrypted';
        } elseif ($this->manager->getInfo()->getUnlockedUntil() === 0) {
            $walletStatus = 'Locked';
        } elseif ($this->manager->getInfo()->getUnlockedUntil() > 0) {
            $walletStatus = 'UnpLocked';
        } else {
            $walletStatus = 'Unknown';
        }
        return $this->render('AppBundle:Admin:admin.html.twig', [
            'currentRound' => $currentRound,
            'walletStatus' => $walletStatus
        ]);
    }

    /**
     * @Route("/round/{round}", name="admin_round", defaults={"round" = null})
     * @Template()
     */
    public function roundAction(Request $request, Round $round = null)
    {
        $this->checkIp($request);

        // new round defining
        if (!$round) {
            $round = new Round();
            if (!$currentRound = $this->roundRepo->findOneBy(['started' => true, 'finished' => false])) {
                // first round auto start
                $roundId = 1;
            } else {
                $lastRound = $this->roundRepo->findOneBy([], ['id' => 'desc']);
                $roundId = $lastRound->getId() + 1;
            }
        } else {
            $roundId = $round->getId();
        }
        $form = $this->createForm(new RoundType(), $round);
        if ($request->isMethod('post')) {
            $form->submit($request);
            if ($form->isValid()) {
                if ($roundId == 1) {
                    $round->setStarted(true);
                }
                $this->em->persist($round);
                $this->em->flush();
                $this->roundManager->defineWalletAccount($round);

                return $this->redirect($this->generateUrl('admin'));
            }
        }

        return $this->render('AppBundle:Admin:round.html.twig', [
            'round' => $round,
            'roundId' => $roundId,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param null $encodedPass
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return null|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function checkIp(Request $request, $encodedPass = null)
    {

        $configIp = $this->container->getParameter('admin_ip');
        if ($configIp && $configIp != $request->getClientIp()) {
            throw new AccessDeniedException();
        }
        return null;
    }


}
