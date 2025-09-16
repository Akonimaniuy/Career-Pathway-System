<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Home', ENT_QUOTES, 'UTF-8'); ?></title>

  <!-- Tailwind CDN (quick start) -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
    .container {
      padding: 7px;
    text-align: left;
    display: flex;
    align-items: center;
    }
    .container h2 {
      font-size: 48px;
      font-weight: bold;
      margin-right: 42px;
      margin-top: 16%;
    }
    .container p {
      max-width: 500px;
      line-height: 1.5;
      margin-top: 15px;
    }

    /* Carousel wrapper */
    .carousel-wrapper {
    margin-top: 60px;
    display: flex
;
    align-items: center;
    position: relative;
    justify-content: end;
}

    /* Carousel track */
    .carousel {
      display: flex;
    transition: transform 0.5s ease-in-out;
    width: 100%;
    max-width: 1000px;
    overflow: hidden;
  margin-top: 10%;
    }

    .card {
      min-width: 300px;
      max-width: 300px;
      background: white;
      color: black;
      border-radius: 15px;
      margin: 0 15px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.3);
      flex-shrink: 0;
      overflow: hidden;
      text-align: center;
      transition: transform 0.3s;
    }

    .card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .card h3 {
      margin: 15px 0 5px;
    }

    .card p {
      font-size: 14px;
      padding: 0 15px;
      color: #444;
      min-height: 60px;
    }

    .card a {
      display: inline-block;
      margin: 15px 0 20px;
      padding: 10px 20px;
      background: #166534;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-size: 14px;
    }

    /* Arrows */
    .arrow {
    position: absolute;
    top: 58%;
    transform: translateY(-50%);
    background: #a3ad1b;
    color: white;
    border: none;
    padding: 15px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    z-index: 5;
}

    .arrow.left {
      left: -50px;
    }
    .arrow.right {
      right: -50px;
    }

    .arrow:hover {
      background: #0b3d1b;
    }
</style>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <main class="mt-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlentities($title ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="mt-2 text-gray-600"><?php echo htmlentities($message ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
 
        <div class="container">
  <h2>EXPLORE<br>PATHWAYS</h2>
  <div class="carousel-wrapper">
    <button class="arrow left">&#10094;</button>
    <div class="carousel" id="carousel">
      <div class="card">
        <img src="public/images/agri.jpg" alt="Agriculture">
        <h3>Agriculture</h3>
        <p>Agriculture is the science and practice of cultivating crops and raising animals to produce food, fiber, and other resources. Careers in this field include farming, livestock management, agribusiness, and sustainable food production.</p>
        <a href="#">Explore</a>
      </div>
      <div class="card">
        <img src="public/images/cookery.jpg" alt="Cookery">
        <h3>Cookery</h3>
        <p>Cookery is the art and practice of preparing, cooking, and presenting food. It involves mastering culinary techniques, ensuring food safety, and creating meals that are both nutritious and appealing. A career in cookery can lead to opportunities as a chef, baker, or food entrepreneur in restaurants, hotels, catering services, and other parts of the hospitality industry.</p>
        <a href="#">Explore</a>
      </div>
      <div class="card">
        <img src="public/images/ict.jpg" alt="ICT">
        <h3>ICT</h3>
        <p>ICT focuses on the use of technology to manage, process, and share information. It covers computer systems, networks, software, and digital communication tools that are essential in today’s industries. A career in ICT can lead to roles such as software developer, network administrator, IT support specialist, or web designer.</p>
        <a href="#">Explore</a>
      </div>
      <div class="card">
        <img src="public/images/electrical.jpg" alt="Electrical">
        <h3>Electrical</h3>
        <p>Electrical focuses on the study and application of electricity, electronics, and power systems. It involves installing, maintaining, and repairing electrical wiring, equipment, and machinery. Careers in this field include electricians, electrical technicians, and engineers who work in construction, manufacturing, and energy industries.</p>
        <a href="#">Explore</a>
      </div>
      <div class="card">
        <img src="public/images/smaw.jpg" alt="Welding">
        <h3>Welding</h3>
        <p>Welding is a fabrication process that joins materials, usually metals or thermoplastics. It involves using high heat to melt the parts together, allowing them to cool and form a strong bond. Careers in welding include welders, fabricators, and inspectors who work in construction, manufacturing, shipbuilding, and automotive industries.</p>
        <a href="#">Explore</a>
        <button class="arrow right">&#10095;</button>
      </div>
    </div>
      </div>
    </main>
  </div>
  <script>
  const carousel = document.getElementById('carousel');
  const prevBtn = document.querySelector('.arrow.left');
  const nextBtn = document.querySelector('.arrow.right');
  let scrollAmount = 0;

  prevBtn.addEventListener('click', () => {
    carousel.scrollBy({left: -320, behavior: 'smooth'});
  });

  nextBtn.addEventListener('click', () => {
    carousel.scrollBy({left: 320, behavior: 'smooth'});
  });
</script>
</body>
</html>