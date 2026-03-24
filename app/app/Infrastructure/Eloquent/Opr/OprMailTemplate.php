<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Opr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * opr_mail_templates テーブルに対応するモデル
 *
 * @property int         $id
 * @property string      $template_name
 * @property string      $subject
 * @property string      $body
 * @property array|null  $placeholders
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class OprMailTemplate extends Model
{
    use SoftDeletes;

    protected $connection = 'mst';

    protected $table = 'opr_mail_templates';

    protected $fillable = [
        'template_key',
        'template_name',
        'subject',
        'body',
        'placeholders',
    ];

    protected $casts = [
        'placeholders' => 'array',
    ];
}