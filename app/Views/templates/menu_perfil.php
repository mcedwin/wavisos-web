       <h5>Ajustes</h5>
       <ul class="list-group">
           <?php
            $uri = service('uri');
            foreach ($menu_user as $m) :
                $active = "";
                if (preg_match("#{$m['base']}#i", $uri->getPath())) $active = "active";
            ?>
               <a href="<?php echo base_url($m['url']) ?>" class="list-group-item list-group-item-action <?php echo $active; ?>" aria-current="true"><?php echo $m['name']; ?></a>
           <?php
            endforeach;
            ?>
       </ul>