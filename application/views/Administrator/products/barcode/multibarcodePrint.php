<style>
    .article {
        min-height: 65px;
        max-height: 100px;
        float: left;
        writing-mode: tb-rl;
        line-height: 0;
        font-weight: 700;
        transform: rotate(180deg);
    }

    .content {
        width: 120px;
        float: left;
        padding: 2px;
    }

    .name {
        height: auto;
        width: 120px;
        font-size: 11px;
    }

    .img {
        height: 60px;
        width: 120px;
    }

    .pid {
        height: 15px;
        width: 120px;
    }

    .price {
        height: 10px;
        width: 120px;
    }

    .date {
        height: 90px;
        width: 20px;
        float: right;
        writing-mode: tb-rl;
    }

    .mytext {
        height: 25px !important;
        padding: 2px;
    }

    @media print {
        .page-break {
            display: block;
            page-break-before: always;
        }
    }
</style>

<div id="productList">
    <div class="row" style="display: flex;justify-content:center;">
        <div class="col-md-10 text-right">
            <a style="cursor: pointer;" onclick="printpage(event)">
                <i class="fa fa-print"></i> Print
            </a>
        </div>
    </div>
    <div class="row" style="display: flex;justify-content:center;">
        <div class="output col-md-8 page-break">
            <?php
            if ($this->session->userdata('single') == false) {
                foreach ($products as $item) {
                    for ($i = 0; $i < $item->quantity; $i++) {
            ?>
                        <div style="padding:3px;float: left; height: 95px; width: 140px; border: 1px solid #ddd;">
                            <div style="width: 140px; text-align: center; float: right;">
                                <p class="article" style="font-size: 12px;margin:0; margin-left: <?= $item->article != '' ? '13px !important' : '5px !important' ?>"><?= $item->article ?></p>
                                <p style="font-size: 10px;margin:0px 0px 2px 1px;padding:2px 0 0 0;font-weight: bolder;text-align: center;line-height: 1;"><?= mb_strimwidth($item->name, 0, 35, '...') ?></p>
                                <img src='<?php echo site_url(); ?>GenerateBarcode/<?= $item->code; ?>' style="height: 25px; width: 120px;" />
                                <p style="margin:0;font-size: 12px;margin-top:-3px; text-align: center;font-weight: 900;"><?= $item->code ?></p>
                                <p style="margin:0;margin-top:-1px;text-align: center;font-size: 12px;font-weight: bolder;"><?= $this->session->userdata('Currency_Name'); ?> <?= $item->sale_rate ?></p>
                            </div>
                        </div>
                    <?php
                    }
                }
            } else {
                foreach ($products as $item) {
                    for ($i = 0; $i < $item->quantity; $i++) {
                    ?>
                        <div style="float:left;margin:0px;padding:0; overflow:hidden;border:1px solid #ccc;box-sizing:border-box;border-bottom:none;width: <?= $this->session->userdata('xAxis') ?>in; height: <?= $this->session->userdata('yAxis') ?>in;">
                            <div style="text-align: center;margin:0;padding:0px 0px 0px 0px;width: <?= $this->session->userdata('xAxis') ?>in; height: <?= $this->session->userdata('yAxis') ?>in;">
                                <p class="article" style="font-size: 12px;margin:0;margin-left: <?= $item->article != '' ? '13px !important' : '5px !important' ?>"><?= $item->article ?></p>
                                <p style="font-size: 10px;margin:0px 0px 2px 1px;padding:2px 0 0 0;font-weight: bolder;text-align: center;line-height: 1;"><?= $item->name ?></p>
                                <img src='<?php echo site_url(); ?>GenerateBarcode/<?= $item->code; ?>' style="height: 36px; width: 120px;" />
                                <p style="margin:0;font-size: 12px;margin-top:-3px; text-align: center;font-weight: 900;"><?= $item->code ?></p>
                                <p style="margin:0;margin-top:-1px;text-align: center;font-size: 12px;font-weight: bolder;"><?= $this->session->userdata('Currency_Name'); ?> <?= $item->sale_rate ?></p>
                            </div>
                        </div>
            <?php }
                }
            } ?>
        </div>
    </div>

    <script>
        async function printpage(event) {
            event.preventDefault();

            var printContent = document.querySelector('.output').innerHTML;
            var originalContent = document.body.innerHTML;
            document.body.innerHTML = `
            <style>
                .article {
                    min-height: 65px;
                    max-height: 100px;
                    float: left;
                    writing-mode: tb-rl;
                    line-height: 0;
                    font-weight: 700;
                    transform: rotate(180deg);
                }

                .content {
                    width: 120px;
                    float: left;
                    padding: 2px;
                }

                .name {
                    height: auto;
                    width: 120px;
                    font-size: 11px;
                }

                .img {
                    height: 60px;
                    width: 120px;
                }

                .pid {
                    height: 15px;
                    width: 120px;
                }

                .price {
                    height: 10px;
                    width: 120px;
                }

                .date {
                    height: 90px;
                    width: 20px;
                    float: right;
                    writing-mode: tb-rl;
                }

                .mytext {
                    height: 25px !important;
                    padding: 2px;
                }

                @media print {
                    .page-break {
                        display: block;
                        page-break-before: always;
                    }
                }                
            </style>
            <table style="width:100%;"><tr><td></td>${printContent}</tr></table>
            `;
            await new Promise(resolve => setTimeout(resolve, 1500));
            window.print();
            document.body.innerHTML = originalContent;
        }
    </script>