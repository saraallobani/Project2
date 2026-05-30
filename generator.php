<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db_config.php';
include 'includes/header.php';

$results = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search_trips'])) {
        $budget = filter_input(INPUT_POST, 'budget', FILTER_VALIDATE_FLOAT);
        $start_city = filter_input(INPUT_POST, 'start_city', FILTER_SANITIZE_STRING);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

        $_SESSION['search_budget'] = $budget;
        $_SESSION['search_start_city'] = $start_city;
        $_SESSION['search_category'] = $category;

        if ($budget === false || $budget <= 0) {
            $error = 'يرجى إدخال ميزانية صحيحة.';
            unset($_SESSION['search_results']);
        } else {
            try {
                $query = "SELECT * FROM trips WHERE price <= :budget";
                $params = [':budget' => $budget];

                if (!empty($category) && $category !== 'all') {
                    $query .= " AND category = :category";
                    $params[':category'] = $category;
                }

                $query .= " ORDER BY price DESC";
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $_SESSION['search_results'] = $results;
            } catch (Exception $e) {
                $error = "حدث خطأ أثناء البحث: " . htmlspecialchars($e->getMessage());
                unset($_SESSION['search_results']);
            }
        }
    } elseif (isset($_POST['clear_search'])) {
        unset($_SESSION['search_budget'], $_SESSION['search_start_city'], $_SESSION['search_category'], $_SESSION['search_results']);
        $results = [];
    }
}

$form_data = [
    'budget' => $_SESSION['search_budget'] ?? '',
    'start_city' => $_SESSION['search_start_city'] ?? '',
    'category' => $_SESSION['search_category'] ?? 'all'
];

if (isset($_SESSION['search_results'])) {
    $results = $_SESSION['search_results'];
}
?>

<style>
    :root {
        --primary-color: #ffc107;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
        --info-color: #17a2b8;
        --light-color: #1a1a1a;
        --dark-color: #0f0f0f;
        --bg-color: #0f0f0f;
        --text-color: #ffffff;
        --border-color: #333333;
        --shadow: 0 2px 4px rgba(0,0,0,0.5);
        --transition: all 0.3s ease;
    }

    body {
        background-color: var(--bg-color);
        color: var(--text-color);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
    }

    .search-hero {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.9)),
                    url('https://images.unsplash.com/photo-1547234935-80c7145ec969?q=80&w=1200') center/cover;
        color: white;
        padding: 60px 0;
        text-align: center;
        border-bottom: 3px solid var(--primary-color);
    }

    .search-hero h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .search-hero p {
        font-size: 1.25rem;
        opacity: 0.9;
    }

    .filter-panel {
        background-color: var(--light-color);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 2rem;
        box-shadow: var(--shadow);
        position: sticky;
        top: 20px;
    }

    .filter-panel h5 {
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        font-weight: 600;
    }

    .form-control, .form-select {
        background-color: #252525 !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-color) !important;
        border-radius: 5px;
        padding: 0.75rem;
        font-size: 1rem;
        transition: var(--transition);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color) !important;
        box-shadow: none;
    }

    .btn-search {
        background-color: var(--primary-color);
        border: none;
        color: #000;
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        font-weight: 600;
        transition: var(--transition);
        width: 100%;
    }

    .btn-search:hover {
        background-color: #e0a800;
        transform: translateY(-2px);
    }

    .trip-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2rem;
        box-shadow: var(--shadow);
        border-radius: 10px;
        overflow: hidden;
        background-color: var(--light-color);
        table-layout: fixed; 
    }

    .trip-table th, .trip-table td {
        padding: 1rem;
        text-align: right;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-color) !important;
        white-space: nowrap; 
        overflow: hidden;
        text-overflow: ellipsis; 
    }

    .col-img  { width: 15%; }
    .col-name { width: 30%; }
    .col-loc  { width: 15%; }
    .col-cat  { width: 15%; }
    .col-prc  { width: 13%; }
    .col-det  { width: 12%; }

    .trip-table th {
        background-color: var(--primary-color);
        color: white !important;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .trip-table tr {
    
        content-visibility: auto;
        contain-intrinsic-size: auto 82px;
    }

    .trip-table tr:nth-child(even) {
        background-color: #252525;
    }

    .trip-table tr:hover {
        background-color: #333333;
        transition: var(--transition);
    }


    .img-container {
        width: 80px;
        height: 60px;
        display: block;
        background-color: #252525; 
        border-radius: 5px;
        overflow: hidden;
        position: relative;
    }

    .trip-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }

    .price-badge, .category-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 20px;
        font-size: 0.825rem;
        font-weight: 600;
        text-align: center;
    }

    .price-badge {
        background-color: var(--success-color);
        color: white;
    }

    .category-badge {
        background-color: var(--warning-color);
        color: #000;
    }

    .btn-details {
        background-color: var(--primary-color);
        color: #000;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.875rem;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-details:hover {
        background-color: #e0a800;
        color: #000;
        text-decoration: none;
    }

    .no-results {
        text-align: center;
        padding: 3rem;
        color: #cccccc;
    }

    .no-results i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .alert {
        border-radius: 5px;
        padding: 1rem;
        margin-bottom: 2rem;
        background-color: var(--danger-color);
        color: white;
        border: 1px solid #721c24;
    }

    @media (max-width: 768px) {
        .search-hero h1 {
            font-size: 2rem;
        }

        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .trip-table {
            font-size: 0.875rem;
            min-width: 600px; 
        }
    }
</style>

<div class="search-hero">
    <div class="container">
        <h1>اكتشف الأردن بميزانيتك</h1>
        <p>نظام البحث الذكي للرحلات والوجهات السياحية</p>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-4">
            <div class="filter-panel">
                <h5><i class="fas fa-filter"></i> فلاتر البحث</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">الميزانية القصوى (JOD)</label>
                        <input type="number" name="budget" class="form-control" placeholder="مثلاً: 150" min="1" value="<?= htmlspecialchars($form_data['budget']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">مدينة الانطلاق</label>
                        <select name="start_city" class="form-select">
                            <option value="">اختر المدينة</option>
                            <option value="عمان" <?= $form_data['start_city'] === 'عمان' ? 'selected' : '' ?>>عمان</option>
                            <option value="إربد" <?= $form_data['start_city'] === 'إربد' ? 'selected' : '' ?>>إربد</option>
                            <option value="العقبة" <?= $form_data['start_city'] === 'العقبة' ? 'selected' : '' ?>>العقبة</option>
                            <option value="مأدبا" <?= $form_data['start_city'] === 'مأدبا' ? 'selected' : '' ?>>مأدبا</option>
                        </select>
                    </div>

                    <button type="submit" name="search_trips" class="btn-search">
                        <i class="fas fa-search"></i> تحديث النتائج
                    </button>
                    <button type="submit" name="clear_search" class="btn btn-secondary w-100 mt-2">
                        <i class="fas fa-times"></i> مسح البحث
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($results)): ?>
                <div class="table-responsive">
                    <table class="trip-table">
                        <thead>
                            <tr>
                                <th class="col-img">الصورة</th>
                                <th class="col-name">اسم الرحلة</th>
                                <th class="col-loc">الموقع</th>
                                <th class="col-cat">التصنيف</th>
                                <th class="col-prc">السعر</th>
                                <th class="col-det">التفاصيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $trip): ?>
                                <tr>
                                    <td>
                                        <div class="img-container">
                                            <img src="assets/images/<?= htmlspecialchars($trip['image']) ?>" 
                                                 alt="<?= htmlspecialchars($trip['name'] ?? $trip['title']) ?>" 
                                                 class="trip-image"
                                                 width="80"
                                                 height="60"
                                                 loading="lazy"
                                                 onerror="this.src='https://via.placeholder.com/80x60?text=No+Image'">
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($trip['name'] ?? $trip['title']) ?></td>
                                    <td><i class="fas fa-map-marker-alt text-danger"></i> <?= htmlspecialchars($trip['location']) ?></td>
                                    <td><span class="category-badge"><?= htmlspecialchars($trip['category'] ?? 'عام') ?></span></td>
                                    <td><span class="price-badge"><?= htmlspecialchars($trip['price']) ?> JOD</span></td>
                                    <td>
                                        <a href="trip_details.php?id=<?= htmlspecialchars($trip['id']) ?>" class="btn-details">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h4>ابدأ البحث للعثور على رحلات تناسبك</h4>
                    <p>استخدم الفلاتر الجانبية لتحديد معايير البحث الخاصة بك.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>