<?php

namespace App\Enums;

/**
 * Enum de status da tabela: account_bank_transactions
 */
enum TransactionStatusEnum: string
{
    case Pending             = 'pending';
    case Completed           = 'completed';
    case InsufficientBalance = 'insufficient-balance';
    case NotAuthorized       = 'not-authorized';
}
