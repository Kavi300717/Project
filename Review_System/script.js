 const STORAGE_KEY = "reviews";

    const reviewForm = document.getElementById("reviewForm");
    const reviewsContainer = document.getElementById("reviewsContainer");

    // Loading previous reviews wich are available on local storage
    document.addEventListener("DOMContentLoaded", () => {
      renderReviews();
    });

    // Form Submission handling 
    reviewForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const usernameInput = document.getElementById("username");
      const ratingInput = document.getElementById("rating");
      const commentInput = document.getElementById("comment");

      const username = usernameInput.value.trim();
      const rating = ratingInput.value;
      const comment = commentInput.value.trim();

      if (!username || !rating || !comment) {
        alert("Please fill all fields before submitting.");
        return;
      }

      const review = {
        id: Date.now(),
        username: username,
        rating: Number(rating),
        comment: comment,
        time: new Date().toLocaleString("en-IN", {
          year: "numeric",
          month: "short",
          day: "2-digit",
          hour: "2-digit",
          minute: "2-digit",
          second: "2-digit"
        })
      };

      // Saving reviews to localstorage
      const existing = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
      existing.unshift(review); 
      localStorage.setItem(STORAGE_KEY, JSON.stringify(existing));

      // Clearing the form
      reviewForm.reset();

      // Re-render list
      renderReviews();
    });

    function renderReviews() {
      const reviews = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
      reviewsContainer.innerHTML = "";

      if (reviews.length === 0) {
        const p = document.createElement("p");
        p.className = "no-reviews";
        p.textContent = "No reviews yet. Be the first to add one!";
        reviewsContainer.appendChild(p);
        return;
      }

      reviews.forEach((review) => {
        const card = document.createElement("div");
        card.className = "review-card";

        const header = document.createElement("div");
        header.className = "review-header";

        const usernameSpan = document.createElement("span");
        usernameSpan.className = "review-username";
        usernameSpan.textContent = review.username;

        const timeSpan = document.createElement("span");
        timeSpan.className = "review-time";
        timeSpan.textContent = review.time;

        header.appendChild(usernameSpan);
        header.appendChild(timeSpan);

        const ratingDiv = document.createElement("div");
        ratingDiv.className = "review-rating";
        ratingDiv.textContent = `Rating: ${review.rating} / 5`;

        const commentDiv = document.createElement("div");
        commentDiv.className = "review-comment";
        commentDiv.textContent = review.comment;

        card.appendChild(header);
        card.appendChild(ratingDiv);
        card.appendChild(commentDiv);

        reviewsContainer.appendChild(card);
      });
    }