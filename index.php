<?php

/**
 * Требуется сделать следующее:
 * 1. Добавить типы операции (send - пересылка средств от пользователя к пользователю, метод уже работает,
 * incoming - ввод средств на баланс пользователя, outcoming - вывод средств с баланса)
 * 2. Добавить методы пополнения и вывода средств из системы в userRepository
 * 3. Добавить возможность отмены операции (метод OperationRepository->revert($operation_id)).
 * При этом учесть, что если для отмены операции не хватает средств пользователя, нужно как-то эту операцию поставить
 * в обработку, и если из другой операции или пополнения приходят средства, то первым приоритетом обрабатывать метод revert
 * 4. Добавить дату операции в лог операции и добавить метод в OperationRepository->getOperationsByDate($date)
 * - возврат массива операций по дате,
 * 5. также метод OperationRepository->getOperationSumByDate($date) - возвращает сумму по всем операциям за переданную
 * дату либо сумму за все время если дата не задана
 */

require __DIR__ . '/vendor/autoload.php';

use Payment\Dto\IncomingDto;
use Payment\Dto\OutcomingDto;
use Payment\Dto\SendDto;
use Payment\PaymentService;
use Payment\Policies\PaymentPolicy;
use Payment\Repositories\DebtRepository;
use Payment\Repositories\PaymentRepository;
use Payment\Repositories\UserRepository;
use Payment\RepositoryService;

const STORAGE_DIR = __DIR__ . '/storage';

$repositoryService = new RepositoryService(
    new UserRepository(),
    new PaymentRepository(),
    new DebtRepository()
);
$paymentPolicy = new PaymentPolicy($repositoryService);
$paymentService = new PaymentService($repositoryService, $paymentPolicy);


//$inc1= $paymentService->incomingMoney(new IncomingDto('uniqid1', 20000));
//$res = $paymentService->sendMoney(new SendDto('uniqid1', 'uniqid7', 10000));
//$res2 = $paymentService->sendMoney(new SendDto('uniqid7', 'uniqid6',10000));
//$res3 = $paymentService->outcomingMoney(new OutcomingDto('uniqid6', 10000));
//$rev1 = $paymentService->revert($res->id);
//$rev2 = $paymentService->revert($res2->id);


$inc2 = $paymentService->incomingMoney(new IncomingDto('uniqid6', 10000));
$out1 = $paymentService->outcomingMoney(new OutcomingDto('uniqid1', 20000));
//
//
//$sum1 = $paymentService->getOperationsSumByDate();
//$sum2 = $paymentService->getOperationsSumByDate('05.06.2024');
//$op1 = $paymentService->getOperationsByDate('05.06.2024');

var_dump(11);
