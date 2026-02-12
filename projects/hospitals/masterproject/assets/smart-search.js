document.addEventListener("DOMContentLoaded", function() {
    
    // Config
    const inputs = document.querySelectorAll(".smart-search");

    inputs.forEach(input => {
        // Create Results Container
        const list = document.createElement("div");
        list.className = "search-dropdown";
        input.parentNode.style.position = "relative"; // Ensure proper positioning
        input.parentNode.appendChild(list);

        input.addEventListener("input", function() {
            const query = this.value;
            const type = this.getAttribute("data-type");

            if(query.length < 1) {
                list.style.display = 'none';
                return;
            }

            // Call API
            fetch(`../api/search.php?type=${type}&q=${query}`)
                .then(response => response.json())
                .then(data => {
                    list.innerHTML = "";
                    if (data.length > 0) {
                        list.style.display = 'block';
                        data.forEach(item => {
                            const div = document.createElement("div");
                            div.className = "search-item";
                            div.innerHTML = `<strong>${item.label}</strong><br><small>${item.extra}</small>`;
                            
                            div.addEventListener("click", () => {
                                input.value = item.value;
                                list.style.display = 'none';
                                // Trigger change event if needed by other scripts
                                input.dispatchEvent(new Event('change')); 
                            });
                            
                            list.appendChild(div);
                        });
                    } else {
                        list.style.display = 'none';
                    }
                });
        });

        // Hide on click outside
        document.addEventListener("click", function(e) {
            if (e.target !== input) {
                list.style.display = 'none';
            }
        });
    });
});