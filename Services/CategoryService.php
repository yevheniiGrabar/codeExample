<?php

namespace App\Services;

use App\Models\Category;
use App\Traits\CurrentCompany;

class CategoryService
{
    /**
     * @param array $data
     * @return Category
     * @throws \Exception
     */
    public function createCategory(array $data): Category
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        if (!empty($data['number'])) {
            $usedNumbers = $this->getUsedNumbers($currentCompany->company_id);
            $number = intval($data['number']);

            if (in_array($number, $usedNumbers)) {
                throw new \Exception('Number is not unique for this company.');
            }

            $category = Category::query()->create(
                [
                    'name' => $data['name'],
                    'number' => $number,
                    'company_id' => $currentCompany->company_id,
                ]
            );
        } else {
            // Если число не передано, генерируем уникальное число.
            $uniqueNumber = $this->generateUniqueNumber($currentCompany->company_id);

            $category = Category::query()->create(
                [
                    'name' => $data['name'],
                    'number' => $uniqueNumber,
                    'company_id' => $currentCompany->company_id,
                ]
            );
        }

        return $category;
    }

    /**
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function updateCategory(Category $category, array $data): Category
    {
        $currentCompany = CurrentCompany::getDefaultCompany();

        $category->update(
            [
                'name' => $data['name'] ?? $category->name,
                'number' => $data['number'] ?? $category->number,
                'company_id' => $currentCompany->company_id,
            ]
        );

        $category->save();

        return $category;
    }

    /**
     * @param $companyId
     * @return int
     */
    private function generateUniqueNumber($companyId): int
    {
        $usedNumbers = $this->getUsedNumbers($companyId);

        $maxNumber = 1000;
        $attempts = 0;

        do {
            $randomNumber = mt_rand(1, $maxNumber);

            $uniqueNumber = intval($companyId . str_pad($randomNumber, strlen($maxNumber), '0', STR_PAD_LEFT));

            $attempts++;
        } while (in_array($uniqueNumber, $usedNumbers) && $attempts < $maxNumber);

        $usedNumbers[] = $uniqueNumber;

        // Здесь также нужно сохранить $usedNumbers в базе данных или файле для данной компании

        return $uniqueNumber;
    }


    /**
     * @param int $companyId
     * @return array
     */
    private function getUsedNumbers(int $companyId): array
    {
        // Здесь реализуйте логику получения массива с уже сгенерированными числами для данной компании.
        // Например, выполните запрос к базе данных, чтобы получить все числа для данной компании.

        return Category::query()
            ->where('company_id', $companyId)
            ->pluck('number')
            ->toArray();
    }
}
