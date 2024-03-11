<?php

include("../db/search.php");

?>
<nav class="js-header">
<div class="container">
<div class="header">
<div class="header-first">
<a class="logo-link" href="http://localhost/infinityware/index">
    <div class="logo">
        <img src="http://localhost/infinityware/images/Infinitywarelogo.png" alt="">
    </div>
</a>
</div>
<div class="header-middle">
    <div class="header-items">
        <a style="text-decoration: none;" href="http://localhost/infinityware/c"><p>Products</p></a>
    </div><div class="header-items">
        <a style="text-decoration: none;" href="http://localhost/infinityware/tracking"><p>Tracking</p></a>
    </div>
    <div class="header-items">
        <a style="text-decoration: none;" href="http://localhost/infinityware/PC-Builder"><p>PC Builder</p></a>
    </div>
</div>
<div class="header-space">
    
</div>
<div class="header-search">
<div class="search-bar">
    <form class="search-form" action="" method="GET">
        <input class="search-input" type="text" name="query" placeholder="Enter your search query">
        <button class="search-submit" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
</div>
</div>

<div class="header-last">
<div class="header-items">
    <a class="cart-link" href="http://localhost/infinityware/cart">
        <div class="cart" id="items">
            <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
        </div>
        <div class="cart-icon-items"> 
            <p class="icon-items" style="color: white;"><?php echo $total_quantity; ?></p>
            </div>
        
    </a>

</div>
</div>
</div>
</nav>

