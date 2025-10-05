<div class="d-flex justify-content-between align-items-center">
    <div>
        <p><?php
            $no=$no_of_records_per_page*$pageno;
            $noInit=$no-$no_of_records_per_page+1;
            $noInit2=$no-$no_of_records_per_page+$c0->num_rows;
            echo"$noInit - $noInit2 ($total_rows)";?>
        </p>
    </div>

    <div class="d-flex justify-content-end mt-3">
        <nav aria-label="Page navigation example">
            <ul class="pagination pagination-sm">
                <li class="page-item"><a class="page-link h-100 d-flex align-items-center <?php if($pageno <= 1){ echo 'disabled'; } ?>" href="?pageno=1"><i class="bi bi-chevron-double-left"></i></a></li>
                <li class="page-item"><a class="page-link h-100 d-flex align-items-center" href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>"><i class="bi bi-chevron-left"></i></a></li>
                <li class="page-item"><a class="page-link" ><?=$pageno;?></a></li>
                <li class="page-item"><a class="page-link h-100 d-flex align-items-center" href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>"><i class="bi bi-chevron-right"></i></a></li>
                <li class="page-item"><a class="page-link h-100 d-flex align-items-center <?php if($total_pages == 0){ echo 'disabled'; } ?>" href="<?php if($total_pages > 0){ ?>?pageno=<?= $total_pages; ?> <?php } ?>"><i class="bi bi-chevron-double-right"></i></a></li>
            </ul>
        </nav>
    </div>
</div>