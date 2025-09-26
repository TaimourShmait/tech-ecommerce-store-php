<?php
function getCartItems($pdo, $user_id)
{
    $sql = "SELECT 
        ci.id as cart_item_id,
        ci.quantity,
        ci.added_at,
        p.id as product_id,
        p.name as product_name,
        p.price,
        p.cover_image,
        sc.name as subcategory_name,
        c.name as category_name
    FROM cart_items ci
    INNER JOIN products p ON ci.product_id = p.id
    INNER JOIN product_subcategories ps ON p.id = ps.product_id
    INNER JOIN subcategories sc ON ps.subcategory_id = sc.id
    INNER JOIN category_subcategories cs ON sc.id = cs.subcategory_id
    INNER JOIN categories c ON cs.category_id = c.id
    WHERE ci.user_id = :user_id
    ORDER BY ci.added_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get cart summary (total items, total price)
 */
function getCartSummary($pdo, $user_id)
{
    $sql = "SELECT 
        COUNT(ci.id) as item_count,
        SUM(ci.quantity) as total_quantity,
        SUM(ci.quantity * p.price) as total_price
    FROM cart_items ci
    INNER JOIN products p ON ci.product_id = p.id
    WHERE ci.user_id = :user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get cart item count for navbar badge!!!
 */
function getCartItemCount($pdo, $user_id)
{
    $sql = "SELECT SUM(quantity) as total_items FROM cart_items WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_items'] ? (int)$result['total_items'] : 0;
}

/**
 * Check if product is already in cart
 */
function isProductInCart($pdo, $user_id, $product_id)
{
    $sql = "SELECT quantity FROM cart_items WHERE user_id = :user_id AND product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
