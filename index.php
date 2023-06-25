<?php 
session_start();
$product_ids = array();

//chech if Add to Cart button has been submitted
if(filter_input(INPUT_POST, 'add_to_cart')){
    if(isset($_SESSION['shopping_cart'])){
        //keep track of how many products in the cart
        $count = count($_SESSION['shopping_cart']);
        
        //create sequential array for matching array keys to product ids
        $product_ids = array_column($_SESSION['shopping_cart'], 'id');

        if (!in_array(filter_input(INPUT_GET, 'id'), $product_ids)){
            $_SESSION['shopping_cart'][$count]= array
            (
                'id'=> filter_input(INPUT_GET, 'id'),
                'name'=> filter_input(INPUT_POST, 'name'),
                'price'=> filter_input(INPUT_POST, 'price'),
                'quantity'=> filter_input(INPUT_POST, 'quantity')
            );
        }
        else { //product already exist? increase quantity
            //match array key to id of the product added to cart
            for($i = 0; $i < count($product_ids); $i++){
                if ($product_ids[$i]== filter_input(INPUT_GET, 'id')){

                    //add item quantity to the existing product in array
                    $_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
                }
            }
        }


    } else{  //if shopping cart doesnt exist create first product with arr key 0
            //create array using submitted form data starting from key 0 and fill it with values
            $_SESSION['shopping_cart'][0]= array
            (
                'id'=> filter_input(INPUT_GET, 'id'),
                'name'=> filter_input(INPUT_POST, 'name'),
                'price'=> filter_input(INPUT_POST, 'price'),
                'quantity'=> filter_input(INPUT_POST, 'quantity')
            );

    }
}

if(filter_input(INPUT_GET, 'action') == 'delete'){
    //loop through all products in cart until match with GET id var
    foreach($_SESSION['shopping_cart'] as $key => $product){
       if($product['id'] == filter_input(INPUT_GET, 'id')){
        //remove product from cart when it match GET id
        unset($_SESSION['shopping_cart'][$key]);
       } 
    }
    //reset session arr kaeys so they match with product_ids numeric arr
    $_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
}

//print_r($_SESSION);

function pre_r($array){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}
?>


<!DOCTYPE html>
<html> 

    <head> 
        <title>Shopping Cart</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="cart.css"/>
    </head>
    <body>
        <div class="container">

        <?php
        $serverName = "localhost";
        $userName = "root";
        $password = "";
        $dbName = "cart";
        //create connection I
        $connect = mysqli_connect($serverName, $userName, $password, $dbName);
        $query = 'SELECT * FROM products ORDER by id ASC';
        $result = mysqli_query($connect, $query);

        if ($result):
            if(mysqli_num_rows($result)>0):
                while($product = mysqli_fetch_assoc($result)):
                   // print_r($product);
                ?>
                <div class="col-sm-4 col-md-3">
                    <form method="post" action="index.php?action=add&id=<?php echo $product['id']; ?>">
                        <div class="products">
                            <img src="<?php echo $product['image'];?>" class="img-responsive"/>
                            <h4 class="text-info"> <?php echo $product['name']; ?> </h4>
                            <h4>E£ <?php echo $product['price']; ?> </h4>
                            <input type="text" name="quantity" class="form-control" value="1" />
                            <input type="hidden" name="name" value="<?php echo $product['name']; ?>" />
                            <input type="hidden" name="price" value="<?php echo $product['price']; ?>" />
                            <input type="submit" name="add_to_cart" style="margin-top: 5px;" class="btn-info" 
                            value="Add To Cart" />

                        </div>
                    </form>
                </div>
                <?php 
                endwhile;
            endif;
        endif;
        ?>
        <div style="clear:both"></div>
        <br />
        <div class="table-responsive">
        <table class="table">
        <tr><th colspan="5"><h3>Order Details</h3></th></tr>
        <tr>
        <th width="40%">Product Name</th>
        <th width="10%">Quantity</th>
        <th width="20%">Price</th>
        <th width="15%">Total</th>
        <th width="5%">Action</th>
        </tr>
        <?php
        if (!empty($_SESSION['shopping_cart'])):
        $total = 0;
        foreach($_SESSION['shopping_cart'] as $key => $product):
        ?>    
        <tr>
        <td><?php echo $product['name']; ?></td>
        <td><?php echo $product['quantity']; ?></td>
        <td>E£ <?php echo $product['price']; ?></td>
        <td>E£ <?php echo number_format($product['quantity'] * $product['price'], 2); ?></td>
        <td>
        <a href="index.php?action=delete&id=<?php echo $product['id']; ?>">
        <div class="btn-danger">Remove</div>
        </a>
        </td>
        </tr>
        <?php
            $total = $total + ($product['quantity'] * $product['price']);
            endforeach;
        ?>    
            <tr>
            <td colspan="3" align="right">Total</td>
            <td align="right">E£ <?php echo number_format($total, 2); ?></td>
            <td></td>
            </tr>
            <tr>
            <!-- Show checkout button only if the shopping cart is not empty -->
            <td colspan="5">
            <?php
            if (isset($_SESSION['shopping_cart'])):
            if (count($_SESSION['shopping_cart']) > 0):
            ?>
            <a href="#" class="button">Checkout</a>
            <?php endif; endif; ?>
            </td>
            </tr>
            <?php
            endif;
            ?>
            </table>
            </div>



        </div>    
    </body>

</html>


