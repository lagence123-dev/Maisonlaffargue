<?php

use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class colissimoOverride extends colissimo
{
    public function hookActionOrderGridDefinitionModifier(array $params)
    {
        /** @var GridDefinitionInterface $definition */
        $definition = $params['definition'];
        /** @var ColumnCollection */
        $columns = $definition->getColumns();
        $columnCarrier = new DataColumn('carrier_name');
        $columnCarrier->setName($this->l('Transporteur'));
        $columnCarrier->setOptions([
            'field' => 'carrier_name',
        ]);
        $columns->addAfter('date_add', $columnCarrier);
        /** @var FilterCollectionInterface $filters */
        $filters = $definition->getFilters();
        $filterCarrier = new Filter('carrier_name', TextType::class);
        $filterCarrier->setAssociatedColumn('carrier_name');
        $filterCarrier->setTypeOptions(['required' => false]);
        $filters->add($filterCarrier);

        parent::hookActionOrderGridDefinitionModifier($params);
    }

    public function hookActionOrderGridQueryBuilderModifier(array $params)
    {
        /** @var QueryBuilder $searchQueryBuilder */
        $searchQueryBuilder = $params['search_query_builder'];
        $searchQueryBuilder->addSelect('ca.name as carrier_name')
            ->leftJoin('o', _DB_PREFIX_ . 'carrier', 'ca', 'ca.id_carrier = o.id_carrier');
        /** @var SearchCriteriaInterface $searchCriteria */
        $searchCriteria = $params['search_criteria'];
        $strictComparisonFilters = [
            'carrier_name' => '(ca.name)',
        ];
        $filters = $searchCriteria->getFilters();
        foreach ($filters as $filterName => $filterValue) {
            if (isset($strictComparisonFilters[$filterName])) {
                $alias = $strictComparisonFilters[$filterName];
                $searchQueryBuilder->andWhere("$alias LIKE '%$filterValue%'");
                $searchQueryBuilder->setParameter($filterName, $filterValue);
                continue;
            }
        }

        parent::hookActionOrderGridQueryBuilderModifier($params);
    }
}