<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $words = [
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Hello', 'translation' => 'Здравствуйте'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Yes', 'translation' => 'Да'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'No', 'translation' => 'Нет'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Please', 'translation' => 'Пожалуйста'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Thank you', 'translation' => 'Спасибо'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Sorry', 'translation' => 'Извините'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Goodbye', 'translation' => 'До свидания'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Love', 'translation' => 'Любовь'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Family', 'translation' => 'Семья'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Friend', 'translation' => 'Друг'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Food', 'translation' => 'Еда'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Water', 'translation' => 'Вода'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Coffee', 'translation' => 'Кофе'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Tea', 'translation' => 'Чай'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Work', 'translation' => 'Работа'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Home', 'translation' => 'Дом'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'School', 'translation' => 'Школа'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Happy', 'translation' => 'Счастливый'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Sad', 'translation' => 'Грустный'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Big', 'translation' => 'Большой'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Small', 'translation' => 'Маленький'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Fast', 'translation' => 'Быстрый'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Slow', 'translation' => 'Медленный'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Sun', 'translation' => 'Солнце'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Rain', 'translation' => 'Дождь'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Snow', 'translation' => 'Снег'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Day', 'translation' => 'День'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Night', 'translation' => 'Ночь'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Car', 'translation' => 'Машина'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'City', 'translation' => 'Город'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Country', 'translation' => 'Страна'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Street', 'translation' => 'Улица'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Book', 'translation' => 'Книга'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Computer', 'translation' => 'Компьютер'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Phone', 'translation' => 'Телефон'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Chair', 'translation' => 'Стул'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Table', 'translation' => 'Стол'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Door', 'translation' => 'Дверь'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Window', 'translation' => 'Окно'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'House', 'translation' => 'Дом'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'River', 'translation' => 'Река'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Mountain', 'translation' => 'Гора'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Forest', 'translation' => 'Лес'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Ocean', 'translation' => 'Океан'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Dog', 'translation' => 'Собака'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Cat', 'translation' => 'Кошка'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Bird', 'translation' => 'Птица'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Fish', 'translation' => 'Рыба'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Window', 'translation' => 'Окно'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Chair', 'translation' => 'Стул'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Table', 'translation' => 'Стол'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Pen', 'translation' => 'Ручка'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Paper', 'translation' => 'Бумага'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Shoes', 'translation' => 'Обувь'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Clothes', 'translation' => 'Одежда'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Bag', 'translation' => 'Сумка'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Flower', 'translation' => 'Цветок'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Tree', 'translation' => 'Дерево'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Sky', 'translation' => 'Небо'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Cloud', 'translation' => 'Облако'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Star', 'translation' => 'Звезда'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'River', 'translation' => 'Река'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Ocean', 'translation' => 'Океан'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Lake', 'translation' => 'Озеро'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Bird', 'translation' => 'Птица'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Dog', 'translation' => 'Собака'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Cat', 'translation' => 'Кошка'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Fish', 'translation' => 'Рыба'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'House', 'translation' => 'Дом'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Room', 'translation' => 'Комната'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Kitchen', 'translation' => 'Кухня'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Bed', 'translation' => 'Кровать'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Car', 'translation' => 'Машина'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Bicycle', 'translation' => 'Велосипед'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Bus', 'translation' => 'Автобус'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Train', 'translation' => 'Поезд'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Airplane', 'translation' => 'Самолёт'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Morning', 'translation' => 'Утро'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Evening', 'translation' => 'Вечер'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Week', 'translation' => 'Неделя'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Month', 'translation' => 'Месяц'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Year', 'translation' => 'Год'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Time', 'translation' => 'Время'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Clock', 'translation' => 'Часы'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Work', 'translation' => 'Работа'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'School', 'translation' => 'Школа'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Teacher', 'translation' => 'Учитель'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Student', 'translation' => 'Студент'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Book', 'translation' => 'Книга'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Library', 'translation' => 'Библиотека'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'City', 'translation' => 'Город'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Village', 'translation' => 'Деревня'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Street', 'translation' => 'Улица'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Hospital', 'translation' => 'Больница'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Doctor', 'translation' => 'Доктор'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Money', 'translation' => 'Деньги'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Shop', 'translation' => 'Магазин'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Market', 'translation' => 'Рынок'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Food', 'translation' => 'Еда'],
            ['language_from_id'=>1, 'language_to_id' =>2 ,'word' => 'Drink', 'translation' => 'Напиток']
        ];

        foreach ($words as $word) {
            try {


                DB::table('top_words')->insert($word);
            }catch(\Exception $exception){

            }
        }
    }
}
