<aside>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <?php if ($userRole == 'admin') { ?>
        <li><a href="admin.php">Admin Panel</a></li>
        <?php } ?>
        <?php if ($userRole == 'operator') { ?>
        <li><a href="operator.php">Operator Panel</a></li>
        <?php } ?>
    </ul>
</aside>
