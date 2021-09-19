<?php
require_once('../config.php');
require_once(BASE_PATH . '/logic/posts.php');
require_once(BASE_PATH . '/logic/tags.php');
require_once(BASE_PATH . '/logic/categories.php');
require_once('../dal/basic_dal.php');
function getUserId()
{
    if (session_status() != PHP_SESSION_ACTIVE) session_start();
    if (isset($_SESSION['user'])) return $_SESSION['user']['id'];
    return 0;
}

$tags = getTags();
$categories = getCategories();
$postID = $_GET['id'];
if (isset($_REQUEST['title'])) {
    $errors = validatePostCreate($_REQUEST);
    if (count($errors) == 0) {  
        $updatedDate = date('Y-m-d H:i:s');
        $sql = "UPDATE posts 
                SET title ='" .$_REQUEST['title'] . "'  ,
                    content ='" .$_REQUEST['content'] . "'  ,
                    category_id ='" .$_REQUEST['category_id'] . "'  ,
                    publish_date ='" .$_REQUEST['publish_date'] . "'  ,
                    updated_at ='" .$updatedDate . "'  
                    WHERE id= " . $postID;
        
        //echo $sql;
        editData($sql);
       // var_dump($sql);
        header('Location:index.php');
            die();
    }
}    
/*                
if (isset($_REQUEST['title'])) {
    $errors = validatePostCreate($_REQUEST);
    if (count($errors) == 0) {
        $sql = "UPDATE posts 
                SET title ='" .$_REQUEST['title'] . "'  And
                    content ='" .$_REQUEST['content'] . "'  And
                WHERE id= " . $postID;   
                   
        if (editData($sql) !== '') {
            header('Location:index.php');
            die();
        } else {
            $generic_error = "Error while adding the post";
        }
    }
}*/
$conn = mysqli_connect('localhost', 'root', '', 'blog');// Check connection
if (mysqli_connect_errno())
  echo "Failed to connect to MySQL: " . mysqli_connect_error(); 
$sql = "SELECT p.*,c.name AS category_name,u.name AS user_name, t.name AS tag_name  
FROM posts p
INNER JOIN categories c ON c.id=p.category_id
INNER JOIN users u ON u.id=p.user_id
LEFT JOIN  post_tags pt ON pt.post_id = p.id 
LEFT JOIN  tags t ON pt.tag_id = t.id 
WHERE p.id ='" . $postID . "'";
 
$result = mysqli_query($conn,$sql);


require_once(BASE_PATH . '/layout/header.php');
?>
<!-- Page Content -->
<!-- Banner Starts Here -->
<div class="heading-page header-text">
    <section class="page-heading">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-content">
                        <h4>Add Post</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php 
$row = mysqli_fetch_array($result);
$conn->close();
$format = 'Y-m-d';
/*$date = $row['publish_date'];
//echo  DateTime::createFromFormat('Y-m-d H:i:s', $date)->format('mm/dd/YYYY'); 
$date2 =  date($format, strtotime($row['publish_date']));
*/ 
//var_export($row['category_id']);
//var_export($row['category_name'])  ;  
?>
<!-- Banner Ends Here -->
<section class="blog-posts">
    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="all-blog-posts">
                    <div class="row">
                        <div class="col-sm-12">
                            <form method="POST" enctype="multipart/form-data">               
                                <input name="title" placeholder="title" class="form-control" value = "<?php echo $row['title']; ?>" />
                                <?= isset($errors['title']) ? "<span class='text-danger'>" . $errors['title'] . "</span>" : "" ?>
                                <textarea name="content" class="form-control" > <?php echo  $row['content'];?> </textarea>
                                <?= isset($errors['content']) ? "<span class='text-danger'>" . $errors['content'] . "</span>" : "" ?>
                                <label>Upload Image<input type="file" name="image" /></label><br />
                                <?= isset($errors['image']) ? "<span class='text-danger'>" . $errors['image'] . "</span>" : "" ?>
                                <label>Publish date<input type="date" name="publish_date" class="form-control" value="<?php echo date($format, strtotime($row['publish_date'])) ?>"></label>
                                <?= isset($errors['publish_date']) ? "<span class='text-danger'>" . $errors['publish_date'] . "</span>" : "" ?>
                                <select id ="category_id" name="category_id" class="form-control">">
                                <option value="">Select category</option>
                                    <?php
                                    foreach ($categories as $category) {
                                        $selected = ($row['category_name'] == $category['name']) ? "selected" : "";
                                        echo '<option '.$selected.' value="'.$category['id'].'">'.$category['name'].'</option>';
                                    }
                                    ?>
                                </select>
                                <?= isset($errors['category_id']) ? "<span class='text-danger'>" . $errors['category_id'] . "</span>" : "" ?>
                                <select name="tags[]" multiple class="form-control">
                                    <?php
                                    foreach ($tags as $tag) {
                                        //echo "<option value='{$tag['id']}'>{$tag['name']}</option>";
                                       $selected = ($row['tag_name'] == $tag['name']) ? "selected" : "";
                                      // echo "<option             value='{$tag['id']}'>{$tag['name']}</option>";
                                       //echo '<option '.$selected.' value={"'.$tag['id'].'}">{'.$tag['name'].'}</option>';
                                       echo '<option '.$selected.' value="'.$tag['id'].'">'.$tag['name'].'</option>';
                                    }
                                    ?>
                                </select>
                                <?= isset($errors['tags']) ? "<span class='text-danger'>" . $errors['tags'] . "</span>" : "" ?>
                                <button class="btn btn-success">Update Post</button>
                                <a href="index.php" class="btn btn-danger">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once(BASE_PATH . '/layout/footer.php') ?>