<?php

namespace App\Twig\Components;

use App\Entity\User;
use App\Repository\InvoiceRepository;
use DateTimeImmutable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart as ModelChart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Chart
{
    use DefaultActionTrait;

    public ModelChart $chart;

    #[LiveProp]
    public array $years = [];

    #[LiveProp(writable: true)]
    public int $currentYear;

    public function __construct(
        private ChartBuilderInterface $chartBuilder,
        private InvoiceRepository $invoiceRepository,
        private Security $security,
    ) {
    }

    public function mount(): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException();
        }

        $date = new DateTimeImmutable();
        $this->currentYear = (int) $date->format('Y');

        $this->years = [];
        for ($i = 0; $i < 20; $i++) {
            $this->years[] = $this->currentYear - $i;
        }

        $this->buildChart($user);
    }

    #[LiveAction]
    public function changeYear(): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException();
        }

        $this->buildChart($user);
    }

    private function buildChart(User $user): void
    {
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $date = new DateTimeImmutable($this->currentYear . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01');
            $data[] = $this->invoiceRepository->getTotalPaidByMonth($user, $date);
        }

        $this->chart = $this->chartBuilder->createChart(ModelChart::TYPE_BAR);
        $this->chart->setData([
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Déc'],
            'datasets' => [
                [
                    'label' => 'Chiffre d\'affaires',
                    'data' => $data,
                    'backgroundColor' => '#155DFC',
                ]
            ]
        ]);
    }

    public function getChart(): ModelChart
    {
        return $this->chart;
    }

}
