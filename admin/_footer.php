    </main>
  </div>
  <?php if (!empty($adminScripts) && is_array($adminScripts)) : foreach ($adminScripts as $scriptSrc) : ?>
  <script src="<?php echo mmh_h($scriptSrc); ?>" referrerpolicy="origin"></script>
  <?php endforeach; endif; ?>
  <?php if (!empty($adminFooterHtml)) echo $adminFooterHtml; ?>
</body>
</html>
