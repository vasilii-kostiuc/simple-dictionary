<?php

namespace Database\Seeders;

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
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'time', 'translation' => 'время'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'year', 'translation' => 'год'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'people', 'translation' => 'люди'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'way', 'translation' => 'путь'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'day', 'translation' => 'день'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'man', 'translation' => 'мужчина'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'thing', 'translation' => 'вещь'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'woman', 'translation' => 'женщина'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'life', 'translation' => 'жизнь'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'child', 'translation' => 'ребенок'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'world', 'translation' => 'мир'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'school', 'translation' => 'школа'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'state', 'translation' => 'государство'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'family', 'translation' => 'семья'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'student', 'translation' => 'студент'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'group', 'translation' => 'группа'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'country', 'translation' => 'страна'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'problem', 'translation' => 'проблема'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'hand', 'translation' => 'рука'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'part', 'translation' => 'часть'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'place', 'translation' => 'место'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'case', 'translation' => 'случай'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'week', 'translation' => 'неделя'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'company', 'translation' => 'компания'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'system', 'translation' => 'система'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'program', 'translation' => 'программа'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'question', 'translation' => 'вопрос'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'work', 'translation' => 'работа'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'government', 'translation' => 'правительство'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'number', 'translation' => 'число'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'night', 'translation' => 'ночь'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'point', 'translation' => 'точка'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'home', 'translation' => 'дом'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'water', 'translation' => 'вода'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'room', 'translation' => 'комната'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'mother', 'translation' => 'мать'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'area', 'translation' => 'область'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'money', 'translation' => 'деньги'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'story', 'translation' => 'история'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'fact', 'translation' => 'факт'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'month', 'translation' => 'месяц'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'lot', 'translation' => 'много'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'right', 'translation' => 'право'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'study', 'translation' => 'исследование'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'book', 'translation' => 'книга'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'eye', 'translation' => 'глаз'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'job', 'translation' => 'работа'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'word', 'translation' => 'слово'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'business', 'translation' => 'бизнес'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'issue', 'translation' => 'вопрос'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'side', 'translation' => 'сторона'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'kind', 'translation' => 'тип'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'head', 'translation' => 'голова'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'house', 'translation' => 'дом'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'service', 'translation' => 'услуга'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'friend', 'translation' => 'друг'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'father', 'translation' => 'отец'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'power', 'translation' => 'власть'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'hour', 'translation' => 'час'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'game', 'translation' => 'игра'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'line', 'translation' => 'линия'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'end', 'translation' => 'конец'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'member', 'translation' => 'член'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'law', 'translation' => 'закон'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'car', 'translation' => 'машина'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'city', 'translation' => 'город'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'community', 'translation' => 'сообщество'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'name', 'translation' => 'имя'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'president', 'translation' => 'президент'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'team', 'translation' => 'команда'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'minute', 'translation' => 'минута'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'idea', 'translation' => 'идея'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'kid', 'translation' => 'ребенок'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'body', 'translation' => 'тело'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'information', 'translation' => 'информация'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'back', 'translation' => 'спина'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'parent', 'translation' => 'родитель'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'face', 'translation' => 'лицо'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'others', 'translation' => 'другие'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'level', 'translation' => 'уровень'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'office', 'translation' => 'офис'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'door', 'translation' => 'дверь'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'health', 'translation' => 'здоровье'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'person', 'translation' => 'человек'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'art', 'translation' => 'искусство'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'war', 'translation' => 'война'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'history', 'translation' => 'история'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'party', 'translation' => 'вечеринка'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'result', 'translation' => 'результат'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'change', 'translation' => 'изменение'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'morning', 'translation' => 'утро'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'reason', 'translation' => 'причина'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'research', 'translation' => 'исследование'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'girl', 'translation' => 'девочка'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'guy', 'translation' => 'парень'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'moment', 'translation' => 'момент'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'air', 'translation' => 'воздух'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'teacher', 'translation' => 'учитель'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'force', 'translation' => 'сила'],
            ['language_from_id' => 2, 'language_to_id' => 1, 'word' => 'education', 'translation' => 'образование'],
        ];

        DB::table('top_words')->delete();

        foreach ($words as $word) {
            try {
                DB::table('top_words')->insert($word);
            } catch (\Exception $exception) {

            }
        }
    }
}
