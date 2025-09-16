<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'About Us', ENT_QUOTES, 'UTF-8'); ?></title>

  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Fade-in effect for sections */
    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }
    .fade-in.is-visible {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <main class="mt-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-800 fade-in">About Our System</h1>
        <p class="mt-2 text-gray-600 fade-in">The Web-based Career Pathway System with Adaptive Assessment is designed to help students make accurate and personalized decisions about their academic and career paths.</p>

        <section class="mt-6 fade-in">
          <h2 class="text-xl font-semibold text-gray-800 transition-colors hover:text-gray-900">Project Overview</h2>
          <ul class="mt-3 space-y-2 text-gray-700">
            <li class="transition-colors hover:text-gray-900"><span class="font-medium">Project Title:</span> Web-based Career Pathway System with Adaptive Assessment</li>
            <li class="transition-colors hover:text-gray-900"><span class="font-medium">Proponents:</span> Christian Nino Abasolo, Matt Lyndon Duron, Marcham James Balvez, Neil Alfredo Ragas, John Marlowe Sepulveda</li>
            <li class="transition-colors hover:text-gray-900"><span class="font-medium">Project Adviser:</span> JOHNREL M. PAGLINAWAN, MIT</li>
          </ul>
        </section>

        <section class="mt-6 fade-in">
          <h2 class="text-xl font-semibold text-gray-800 transition-colors hover:text-gray-900">Statement of the Problem</h2>
          <p class="mt-3 text-gray-700">Many students are confused or overwhelmed when choosing their future career path or academic track. A recent Training Needs Analysis revealed that a large number of students are still undecided. Students often rely on limited information, peer influence, or family pressure, which can lead to stress and a lack of motivation.</p>
          <p class="mt-3 text-gray-700">Existing systems often use fixed, static questionnaires and lack personalized support for younger students beginning to explore their interests.</p>
        </section>

        <section class="mt-6 fade-in">
          <h2 class="text-xl font-semibold text-gray-800 transition-colors hover:text-gray-900">Project Objectives</h2>
          <h3 class="text-lg font-medium text-gray-800 mt-4 transition-colors hover:text-gray-900">Main Objective:</h3>
          <p class="mt-2 text-gray-700">To design and develop a web-based career pathway system with adaptive assessment to support accurate and personalized academic and career path selection for students.</p>
          
          <h3 class="text-lg font-medium text-gray-800 mt-4 transition-colors hover:text-gray-900">Specific Objectives:</h3>
          <ul class="mt-2 list-disc list-inside text-gray-700 space-y-2">
            <li class="transition-colors hover:text-gray-900">Implement an adaptive assessment to evaluate students' interests, skills, and strengths.</li>
            <li class="transition-colors hover:text-gray-900">Develop a recommendation that suggests suitable academic or career paths based on assessment results.</li>
            <li class="transition-colors hover:text-gray-900">Design a user-friendly interface that encourages exploration and informed decision-making.</li>
          </ul>
        </section>
      </div>
    </main>
  </div>

  <script>
    // FADE-IN ON SCROLL LOGIC
    const fadeInElements = document.querySelectorAll('.fade-in');
    
    const observerOptions = {
      root: null,
      rootMargin: '0px',
      threshold: 0.2 // Trigger when 20% of the element is visible
    };
    
    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target); // Stop observing after it's visible
        }
      });
    }, observerOptions);

    fadeInElements.forEach(el => observer.observe(el));
  </script>
</body>
</html>