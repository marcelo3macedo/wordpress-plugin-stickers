<?php
if (!defined('ABSPATH')) exit;

function stickers_add_modal($output) {
    $output .= <<<EOL
        <div id="filter-modal" class="fixed inset-0 z-50 overflow-y-auto w-full h-screen bg-black bg-opacity-30 mx-0 my-0 flex items-center justify-center hidden">
            <div class="relative bg-white rounded-lg shadow-xl w-11/12 md:w-3/4 lg:w-1/2 p-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3 border-b border-gray-200 mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Filtros</h3>
                    <button id="close-modal-btn" class="text-gray-400 hover:text-gray-600 transition-colors duration-300">&times;</button>
                </div>

                <div class="space-y-6">
                    <div class="filter-section">
                        <h4 class="text-lg font-semibold text-gray-700 mb-3">Cor do Cabelo</h4>
                        <div id="hair-colors" class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 md:gap-4">
                            <div data-filter="cabelo:loiro" class="filter-option text-center cursor-pointer p-2 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                <div class="circle w-14 h-14 rounded-full mx-auto mb-1 border-4 border-transparent bg-yellow-300"></div>
                                <span class="text-sm">Loiro</span>
                            </div>
                            <div data-filter="cabelo:castanho" class="filter-option text-center cursor-pointer p-2 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                <div class="circle w-14 h-14 rounded-full mx-auto mb-1 border-4 border-transparent bg-[#7b4b2a]" style="background-color:#7b4b2a"></div>
                                <span class="text-sm">Castanho</span>
                            </div>
                            <div data-filter="cabelo:moreno" class="filter-option text-center cursor-pointer p-2 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                <div class="circle w-14 h-14 rounded-full mx-auto mb-1 border-4 border-transparent bg-black"></div>
                                <span class="text-sm">Moreno</span>
                            </div>
                            <div data-filter="cabelo:grisalho" class="filter-option text-center cursor-pointer p-2 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                                <div class="circle w-14 h-14 rounded-full mx-auto mb-1 border-4 border-transparent bg-gray-400"></div>
                                <span class="text-sm">Grisalho</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button id="apply-filters-btn" class="px-6 py-3 rounded-full bg-indigo-500 text-white text-md font-semibold shadow-lg hover:bg-indigo-600 transition-colors duration-300">
                        Aplicar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    EOL;

    return $output;
}

function stickers_add_modal_script($output) {
    $script = <<<'EOL'
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const filterBtn = document.getElementById("filter-btn");
            const modal = document.getElementById("filter-modal");
            const closeModalBtn = document.getElementById("close-modal-btn");
            const applyFiltersBtn = document.getElementById("apply-filters-btn");
            
            if (filterBtn && modal && closeModalBtn && applyFiltersBtn) {
                filterBtn.addEventListener("click", function() {
                    modal.classList.remove("hidden");
                });

                closeModalBtn.addEventListener("click", function() {
                    modal.classList.add("hidden");
                });

                window.addEventListener("click", function(event) {
                    if (event.target === modal) {
                        modal.classList.add("hidden");
                    }
                });

                applyFiltersBtn.addEventListener("click", function() {
                    modal.classList.add("hidden");
                });
            }
        });

        document.querySelectorAll(".filter-option").forEach(option => {
            option.addEventListener("click", () => {
            option.classList.toggle("selected");

            const circle = option.querySelector(".circle");
            if (option.classList.contains("selected")) {
                circle.classList.add("border-indigo-500");
            } else {
                circle.classList.remove("border-indigo-500");
            }
            });
        });

        document.getElementById("apply-filters-btn").addEventListener("click", () => {
            const selected = Array.from(document.querySelectorAll(".filter-option.selected"))
            .map(opt => opt.getAttribute("data-filter"));
            
            applyFilters(selected);
        });

        function applyFilters(selected) {
            const params = {};

            selected.forEach(item => {
                const [group, value] = item.split(":");
                if (!params[group]) {
                    params[group] = [];
                }
                params[group].push(value);
            });

            const query = Object.entries(params)
                .map(([key, values]) => `${key}=${values.join(",")}`)
                .join("&");

            const url = `?${query}`;            
            window.location.href = url;
        }
        </script>
    EOL;

    return $output . $script;
}