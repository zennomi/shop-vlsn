<div id="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="nav-breadcrumb" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $title; ?></li>
                    </ol>
                </nav>
                <h1 class="page-title"><?= $title; ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="row-custom">
                    <div class="profile-tab-content">
                        <?= view('partials/_messages'); ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th scope="col"><?= trans("order"); ?></th>
                                    <th scope="col"><?= trans("total"); ?></th>
                                    <th scope="col"><?= trans("payment"); ?></th>
                                    <th scope="col"><?= trans("status"); ?></th>
                                    <th scope="col"><?= trans("date"); ?></th>
                                    <th scope="col"><?= trans("options"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?= esc($order->order_number); ?></td>
                                            <td><?= priceFormatted($order->price_total, $order->price_currency); ?></td>
                                            <td>
                                                <?php if ($order->status == 2):
                                                    echo trans("cancelled");
                                                else:
                                                    if ($order->payment_status == 'payment_received'):
                                                        echo trans("payment_received");
                                                    else:
                                                        echo trans("awaiting_payment");
                                                    endif;
                                                endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($order->status == 2): ?>
                                                    <strong class="font-600"><?= trans("cancelled"); ?></strong>
                                                <?php else: ?>
                                                    <strong class="font-600">
                                                        <?php if ($order->payment_status == 'awaiting_payment'):
                                                            if ($order->payment_method == 'Cash On Delivery') {
                                                                echo trans("order_processing");
                                                            } else {
                                                                echo trans("awaiting_payment");
                                                            }
                                                        else:
                                                            if ($order->status == 1):
                                                                echo trans("completed");
                                                            else:
                                                                echo trans("order_processing");
                                                            endif;
                                                        endif; ?>
                                                    </strong>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= formatDate($order->created_at); ?></td>
                                            <td>
                                                <a href="<?= generateUrl('order_details') . '/' . esc($order->order_number); ?>" class="btn btn-sm btn-table-info"><?= trans("details"); ?></a>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (empty($orders)): ?>
                            <p class="text-center">
                                <?= trans("no_records_found"); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row-custom m-t-15">
                    <div class="float-right">
                        <?= view('partials/_pagination'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>