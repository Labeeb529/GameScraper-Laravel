<?php

namespace App\Enums;



enum Game: string
{

    case NFL = 'nfl';
    case NCAAF = 'ncaaf';
    case NCAAB = 'ncaab';

    public function getModelClass(GameDataType $type): string
    {
        return match ([$this, $type]) {
            [self::NFL, GameDataType::Results] => \App\Models\NFLResults::class,
            [self::NFL, GameDataType::Bets] => \App\Models\NFLBets::class,

            [self::NCAAF, GameDataType::Results] => \App\Models\NCAAFResults::class,
            [self::NCAAF, GameDataType::Bets] => \App\Models\NCAAFBets::class,

            [self::NCAAB, GameDataType::Results] => \App\Models\NCAABResults::class,
            [self::NCAAB, GameDataType::Bets] => \App\Models\NCAABBets::class,
        };
    }

    public function getModel(GameDataType $type)
    {
        return $this->getModelClass($type);
    }
}