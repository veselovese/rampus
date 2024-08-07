<header>
    <a class="phone-link" href="../../wall"><img src="../../pics/RampusLogo.svg"><span>Rampus</span></a>
    <?php if (isset($_SESSION['user'])) { 
        $id = $_SESSION['user']['id'];
        $result = $connect->query("SELECT * FROM users WHERE id = $id");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $first_name = $row["first_name"];
                $second_name = $row["second_name"];
                $avatar = $row["avatar"]; 
            }
        } ?>
        <div>
            <a href="../../profile"><?= $first_name . " " . $second_name ?></a>
            <a class="header__avatar" href="../../profile"><img src="../../uploads/avatar/thin_<?= $avatar ?>"></a>
        </div>
    <?php } ?>
</header>