<?
namespace AppBundle\Controller;

use AppBundle\Entity\Link;
use AppBundle\Utils\Numbers\Converters\BasisConverter;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ShortenController extends Controller
{
    protected $ttl;
    protected $ttlDefault;
    protected $basisConverter;

    public function __construct()
    {
        $this->ttl = [
            "seconds10" => "10 seconds",
            "minute" => "1 minute",
            "hour" => "1 hour",
            "day" => "1 day",
            "forever" => "forever",
        ];
        reset($this->ttl);
        $this->ttlDefault = key($this->ttl);
        $this->basisConverter = new BasisConverter();
    }

    /**
     * @Route("/shorten", name="add_link")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $urlToShorten = $request->request->get("urlToShorten");
        $message = "";
        $dateTillLinkExists = new \DateTime();
        $ttlSelected = htmlspecialchars($request->request->get("ttl"));
        if (!in_array($ttlSelected, array_keys($this->ttl))) {
            $ttlSelected = $this->ttlDefault;
        }
        switch ($ttlSelected) {
            case "seconds10":
                $dateTillLinkExists->modify("+30 seconds");
                break;
            case "minute":
                $dateTillLinkExists->modify("+1 minute");
                break;
            case "hour":
                $dateTillLinkExists->modify("+1 hour");
                break;
            case "day":
                $dateTillLinkExists->modify("+1 day");
                break;
            case "forever":
                $dateTillLinkExists = null;
                break;
            default:
                $dateTillLinkExists->modify("+1 minute");
                $ttlSelected = "minute";
        }

        if (!empty($urlToShorten)) {
            $urlToShorten = Link::UrlFormat($urlToShorten);
            // try to fetch existing URL from DB

            /**
             * @var QueryBuilder
             */
            $linkRepository = $this->getDoctrine()
                                   ->getRepository('AppBundle:Link')
            ;

            $link = $linkRepository->findOneBy([
                "url" => $urlToShorten,
            ]);
            $entityManager = $this->getDoctrine()->getEntityManager()
            ;
            // if link exists and not old - print it
            if ($link) {
                if ($link->getDateActiveTo() === null || ($link->getDateActiveTo() !== null && $link->getDateActiveTo() > new \DateTime())) {
                    $shortenedLinkUrl = $this->getShortenedLinkUrl($request, $link);
                    $message = "Shortened version already exists: <br/> <b><a target='_blank' href='//$shortenedLinkUrl'>$shortenedLinkUrl</a></b> <br/> Hits count = " . $link->getCount();
                    if ($ttlSelected !== "forever") {
                        $message .= "<br> Link will become unavailable after " . $link->getDateActiveTo()
                                                                                      ->format(\DateTime::ATOM)
                        ;
                    }
                } else {
                    $entityManager->remove($link);
                    $entityManager->flush();
                    $link = null;
                }
            }

            if (!$link) {
                $link = new Link();
                $link->setUrl($urlToShorten);
                if ($dateTillLinkExists !== null) {
                    $link->setDateActiveTo($dateTillLinkExists);
                }
                $entityManager->persist($link);
                $entityManager->flush();
                $shortenedLinkUrl = $this->getShortenedLinkUrl($request, $link);
                $message = "Shortened version: <br/> <b><a target='_blank' href='//$shortenedLinkUrl'>$shortenedLinkUrl</a></b>";
                if ($ttlSelected !== "forever") {
                    $message .= "<br> This link (and it's stats) will be deleted at " . $link->getDateActiveTo()
                                                                                             ->format(\DateTime::ATOM)
                    ;
                }
            }
        }

        return $this->render(
            'shorten/index.html.twig',
            [
                "urlToShorten" => $urlToShorten,
                "ttl" => $this->ttl,
                "ttlSelected" => $ttlSelected,
                "message" => $message,
            ]
        );
    }

    /**
     * @Route("/q/{hash}", name="query_link")
     * @param Request $request
     *
     * @param         $hash
     *
     * @return Response
     */
    public function qAction(Request $request, $hash)
    {
        if ($this->basisConverter->isConsistFromBasis($hash) === false) {
            $errorText = "Wrong parameter";
        }

        if (!isset($errorText)) {
            /**
             * @var QueryBuilder
             */
            $linkRepository = $this->getDoctrine()
                                   ->getRepository('AppBundle:Link')
            ;

            /**
             * @var Link|null
             */
            $link = $linkRepository->find($this->basisConverter->revert($hash));
            if ($link) {
                $link->incCount();
                $entityManager = $this->getDoctrine()
                                      ->getEntityManager()
                ;
                $entityManager->persist($link);
                $entityManager->flush();

                return $this->redirect($link->getUrl());
            } else {
                $errorText = "Link doesn't exists";
            }
        }

        return $this->render(
            'shorten/q.html.twig',
            [
                "errorText" => $errorText,
            ]
        );
    }

    private function getShortenedLinkUrl(Request $request, Link $link)
    {
        return $request->getHttpHost() . $this->generateUrl(
            "query_link",
            [
                "hash" => $this->basisConverter->convert($link->getId()),
            ]
        );
    }
}