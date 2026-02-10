<?php
namespace App\Domain\Common\Services;

class JapaneseInitialGroupingService
{
    public function __construct()
    {
        mb_regex_encoding('UTF-8');
    }

    /**
     * 車種を頭文字でグループ化
     */
    public function groupByInitial(array $carSeries): array
    {
        $groups = [
            'EN' => [],
            'AA' => [],
            'KA' => [],
            'SA' => [],
            'TA' => [],
            'NA' => [],
            'HA' => [],
            'MA' => [],
            'YA' => [],
            'RA' => [],
            'WA' => [],
        ];
        
        foreach ($carSeries as $carSerie) {
            $seriesName = trim($carSerie->getSeriesName());
            $firstChar = mb_substr($seriesName, 0, 1);
            $groupKey = $this->determineGroup($firstChar);
            
            if ($groupKey) {
                $groups[$groupKey][] = $carSerie;
            }
        }
        
        return $groups;
    }

    private function determineGroup(string $char): ?string
    {
        // 英数字
        if (preg_match('/[A-Za-z0-9]/', $char)) {
            return 'EN';
        }
        
        // ひらがな・カタカナの判定
        $patterns = [
            'AA' => ['あ','い','う','え','お','ア','イ','ウ','エ','オ'],
            'KA' => ['か','き','く','け','こ','が','ぎ','ぐ','げ','ご','カ','キ','ク','ケ','コ','ガ','ギ','グ','ゲ','ゴ'],
            'SA' => ['さ','し','す','せ','そ','ざ','じ','ず','ぜ','ぞ','サ','シ','ス','セ','ソ','ザ','ジ','ズ','ゼ','ゾ'],
            'TA' => ['た','ち','つ','て','と','だ','ぢ','づ','で','ど','タ','チ','ツ','テ','ト','ダ','ヂ','ヅ','デ','ド'],
            'NA' => ['な','に','ぬ','ね','の','ナ','ニ','ヌ','ネ','ノ'],
            'HA' => ['は','ひ','ふ','へ','ほ','ば','び','ぶ','べ','ぼ','ぱ','ぴ','ぷ','ぺ','ぽ','ハ','ヒ','フ','ヘ','ホ','バ','ビ','ブ','ベ','ボ','パ','ピ','プ','ペ','ポ'],
            'MA' => ['ま','み','む','め','も','マ','ミ','ム','メ','モ'],
            'YA' => ['や','ゆ','よ','ヤ','ユ','ヨ'],
            'RA' => ['ら','り','る','れ','ろ','ラ','リ','ル','レ','ロ'],
            'WA' => ['わ','を','ん','ワ','ヲ','ン'],
        ];
        
        foreach ($patterns as $group => $chars) {
            if (in_array($char, $chars, true)) {
                return $group;
            }
        }
        
        return null;
    }
}