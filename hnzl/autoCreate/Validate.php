<?php
/**
 * @author : yhc
 * @date: 2020/11/06 15:33
 * @name:
 */
namespace app\!$module!\validate;
use think\Validate;
class !$name! extends Validate
{
    protected $rule =[
        !$rule!
    ];

    protected $message  = null;
    protected $scene = [
        'add'   => [!$validate!],
        'edit'  => [!$validate!],
        'import'  => [!$validate!],
    ];
    function __construct()
    {
        $this->message = [
            !$msg!
        ];
    }
}