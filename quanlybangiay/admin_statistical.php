<?php

include 'config.php';

class AdminStatistiques
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        session_start();
        $this->verifierSessionAdmin();
    }

    public function verifierSessionAdmin()
    {
        $admin_id = $_SESSION['admin_id'];
        if (!isset($admin_id)) {
            header('location:login.php');
            exit();
        }
    }

    public function getTotalPrice()
    {
        if (isset($_POST['submit'])) {
            $date_from = date($_POST['date_from']);
            $date_to = date($_POST['date_to']);
            $sql_total_price = "SELECT SUM(total_price) AS Total FROM orders WHERE placed_on BETWEEN '$date_from' AND '$date_to';";
        } else {
            $sql_total_price = "SELECT SUM(total_price) AS Total FROM orders;";
        }

        $total_price = $this->conn->query($sql_total_price);
        return $total_price->fetch_assoc()['Total'];
    }

    public function getOutOfStockProducts()
    {
        $sql_out_of_stock = "SELECT * FROM products WHERE quantity = 0";
        $result_stock = $this->conn->query($sql_out_of_stock);
        $out_of_stock = [];

        if ($result_stock->num_rows > 0) {
            while ($row = $result_stock->fetch_assoc()) {
                $out_of_stock[] = $row;
            }
        }

        return $out_of_stock;
    }

    public function getBestSellingProducts()
    {
        $sql_best_seller = "SELECT * FROM products WHERE initial_quantity - quantity > 60";
        $result_seller = $this->conn->query($sql_best_seller);
        $best_seller = [];

        if ($result_seller->num_rows > 0) {
            while ($row = $result_seller->fetch_assoc()) {
                $best_seller[] = $row;
            }
        }

        return $best_seller;
    }
}

class VueAdminStatistiques
{
    private $adminStatistiques;

    public function __construct($adminStatistiques)
    {
        $this->adminStatistiques = $adminStatistiques;
    }

    public function afficherTotalPrix()
    {
        return number_format($this->adminStatistiques->getTotalPrice(), 0, ',', '.') . ' euros';
    }

    public function afficherProduitsEnRuptureDeStock()
    {
        $produitsEnRuptureDeStock = $this->adminStatistiques->getOutOfStockProducts();
        $output = '';

        if (count($produitsEnRuptureDeStock) > 0) {
            $output .= '<div class="table-responsive card mt-2">';
            $output .= '<table style="width: 69% !important; margin: auto;" class="table table-bordered statistical_table">';
            $output .= '<tr>';
            $output .= '<th>ID</th>';
            $output .= '<th>Nom du produit</th>';
            $output .= '<th>Marque</th>';
            $output .= '<th>Description</th>';
            $output .= '<th>Quantité restante</th>';
            $output .= '</tr>';

            foreach ($produitsEnRuptureDeStock as $item) {
                $output .= '<tr>';
                $output .= '<td><label style="width: auto">' . $item['id'] . '</label></td>';
                $output .= '<td><label style="width: auto">' . $item['name'] . '</label></td>';
                $output .= '<td><label style="width: auto">' . $item['trademark'] . '</label></td>';
                $output .= '<td><label style="width: auto">' . $item['describes'] . '</label></td>';
                $output .= '<td><label style="width: auto">' . $item['quantity'] . '</label></td>';
                $output .= '</tr>';
            }

            $output .= '</table>';
            $output .= '</div>';
        } else {
            $output .= '<p class="alert alert-danger">La liste est vide</p>';
        }

        return $output;
    }

    public function afficherMeilleursVentes()
    {
        $meilleursVentes = $this->adminStatistiques->getBestSellingProducts();
        $output = '';

        if (count($meilleursVentes) > 0) {
            $output .= '<div class="table-responsive card mt-2">';
            $output .= '<table style="width: 69% !important; margin: auto;" class="table table-bordered statistical_table">';
            $output .= '<tr>';
            $output .= '<th>ID</th>';
            $output .= '<th>Nom du produit</th>';
            $output .= '<th>Marque</th>';
            $output .= '<th>Description</th>';
            $output .= '<th>Quantité vendue</th>';
            $output .= '</tr>';

            foreach ($meilleursVentes as $item) {
                $output .= '<tr>';
                $output .= '<td><label style="width: auto">' . $item['id'] . '</label></td>';
                $output .= '<td><label style="width: auto">' . $item['name'] . '</label></td>';
                $output .= '<td><label style="width: auto">' . $item['trademark'] . '</label></td>';
                $output .= '<td><label style="width: auto">' . $item['describes'] . '</label></td>';
                $output .= '<td><label style="width: auto">' . ($item['initial_quantity'] - $item['quantity']) . '</label></td>';
                $output .= '</tr>';
            }

            $output .= '</table>';
            $output .= '</div>';
        } else {
            $output .= '<p class="alert alert-danger">La liste est vide</p>';
        }

        return $output;
    }
}

$adminStatistiques = new AdminStatistiques($conn);
$vueAdminStatistiques = new VueAdminStatistiques($adminStatistiques);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Statistiques</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="./css/admin_style.css">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
   <link rel="icon" href="uploaded_img/logo2.png">

    <style>
        .total_price {
            display:flex;
            align-items: center;
            gap: 10px;
        }
        .input-date {
            border: 1px solid;
            border-radius: 2px;
            padding: 4px 7px;
        }
        .send-btn {
            background: blueviolet;
            max-width: 100px;
            text-align: center;
            padding: 4.5px 10px;
            border-radius: 3px;
            color: #fff;
            font-size: 16px;
            margin-left: 7px;
        }
        .send-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<span style="color: #005490;padding-top: 24px; font-weight: bold; display: flex; justify-content: center; font-size: 40px;">STATISTIQUES</span>

   <div class="total_money" style="margin-left: 40px;">
    <h1 style="font-weight: bolder; font-size: 30px;" class="statis_title">Total des revenus</h1>
    <form action="" method="POST">
        De: <input class="input-date" type="date" name="date_from" id="" value="<?php  if(isset($_POST['submit'])) echo $date_from  ?>">
        À: <input class="input-date" type="date" name="date_to" id="" value="<?php  if(isset($_POST['submit'])) echo $date_to  ?>">
        <input style="background-color: #005490;" type="submit" class="send-btn" value="Envoyer" name="submit">
    </form>
    <div class="total_price">
        <h4 style="font-weight: bolder;">Total des revenus des produits vendus: </h4>
        <div style="font-size: 17px;">
            <?php  echo $vueAdminStatistiques->afficherTotalPrix(); ?>
        </div>
    </div>
   </div>
   <!-- GRAPHIQUE JS -->
   <div style="width: 1000px; height:500px; margin-bottom: 80px" class="container">
        <div class="title">
            <h3 style="font-weight: bold;">
                Ventes mensuelles en 2023
            </h3>
        </div>
        <canvas id="canvas" ></canvas>
    </div>
    <!-- Meilleures ventes -->
   <div class="best_seller">
   <h1 style="font-size: 30px; margin-left: 40px;font-weight: bolder;" class="statis_title">Statistiques des meilleures ventes</h1>
   <?php echo $vueAdminStatistiques->afficherMeilleursVentes(); ?>
   </div>
   <div style="margin-bottom: 40px;" class="out_of_stock">
   <h1 style="font-size: 30px;margin-left: 30px; font-weight: bolder;" class="statis_title">Statistiques des produits en rupture de stock</h1>
    <?php echo $vueAdminStatistiques->afficherProduitsEnRuptureDeStock(); ?>
   </div>

   <?php include 'footer.php'; ?>


<script src="js/admin_script.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js" ></script>
<script src="js/chartfix.js" ></script>
</body>
</html>
