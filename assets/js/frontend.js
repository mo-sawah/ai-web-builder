// AI Web Builder Frontend JavaScript
(function ($) {
  "use strict";

  class AIWebBuilder {
    constructor() {
      this.formData = {
        businessType: "",
        industry: "",
        companySize: "",
        targetAudience: "",
        businessGoal: "",
        budget: "",
        businessDescription: "",
        pageCount: "",
        contentStatus: "",
        timeline: "",
        currentWebsite: "",
        existingWebsite: "",
        competitors: "",
        websiteType: "",
        designStyle: "",
        existingBranding: "",
        colorPreference: "",
        designInspiration: "",
        additionalRequirements: "",
        features: [],
      };

      this.currentConcept = null;
      this.isGenerating = false;
      this.init();
    }

    init() {
      this.bindEvents();
      this.validateForm();
      this.initializeTooltips();
    }

    bindEvents() {
      // Website type selection
      $(".awb-option-btn").on("click", (e) => {
        e.preventDefault();
        const $btn = $(e.currentTarget);
        const value = $btn.data("value");

        $(".awb-option-btn").removeClass("selected");
        $btn.addClass("selected");
        this.formData.websiteType = value;
        this.validateForm();
      });

      // Design style selection
      $(".awb-style-btn").on("click", (e) => {
        e.preventDefault();
        const $btn = $(e.currentTarget);
        const value = $btn.data("value");

        $(".awb-style-btn").removeClass("selected");
        $btn.addClass("selected");
        this.formData.designStyle = value;
      });

      // Feature selection
      $(".awb-feature-btn").on("click", (e) => {
        e.preventDefault();
        const $btn = $(e.currentTarget);
        const value = $btn.data("value");

        if (this.formData.features.includes(value)) {
          this.formData.features = this.formData.features.filter(
            (f) => f !== value
          );
          $btn.removeClass("selected");
        } else {
          this.formData.features.push(value);
          $btn.addClass("selected");
        }
        this.updateFeatureCount();
      });

      // Text inputs
      const textInputs = [
        "businessType",
        "targetAudience",
        "businessDescription",
        "existingWebsite",
        "competitors",
        "designInspiration",
        "additionalRequirements",
      ];

      textInputs.forEach((id) => {
        $(`#${id}`).on("input", (e) => {
          this.formData[id] = e.target.value;
          if (id === "businessType") this.validateForm();
        });
      });

      // Select inputs
      const selectInputs = [
        "industry",
        "companySize",
        "businessGoal",
        "budget",
        "pageCount",
        "contentStatus",
        "timeline",
        "currentWebsite",
        "existingBranding",
        "colorPreference",
      ];

      selectInputs.forEach((id) => {
        $(`#${id}`).on("change", (e) => {
          this.formData[id] = e.target.value;
          if (id === "industry") this.validateForm();
        });
      });

      // Form submission
      $("#awb-form").on("submit", (e) => {
        e.preventDefault();
        if (this.validateForm() && !this.isGenerating) {
          this.generateConcept();
        }
      });

      // Keyboard shortcuts
      $(document).on("keydown", (e) => {
        if (
          e.ctrlKey &&
          e.key === "Enter" &&
          this.validateForm() &&
          !this.isGenerating
        ) {
          this.generateConcept();
        }
      });
    }

    validateForm() {
      const isValid = this.formData.businessType && this.formData.industry;
      const $validationMessage = $("#awb-validation-message");
      const $generateBtn = $("#awb-generate-btn");

      if (isValid) {
        $validationMessage.hide();
        $generateBtn.prop("disabled", false).removeClass("awb-btn-disabled");
        return true;
      } else {
        $validationMessage.show();
        $generateBtn.prop("disabled", true).addClass("awb-btn-disabled");
        return false;
      }
    }

    updateFeatureCount() {
      const count = this.formData.features.length;
      const $counter = $(".awb-feature-counter");

      if ($counter.length === 0 && count > 0) {
        $("#awb-generate-btn").after(`
                    <div class="awb-feature-counter" style="margin-top: 0.5rem; color: rgba(255, 255, 255, 0.7); font-size: 0.875rem;">
                        ${count} feature${count !== 1 ? "s" : ""} selected
                    </div>
                `);
      } else if ($counter.length > 0) {
        if (count > 0) {
          $counter.text(`${count} feature${count !== 1 ? "s" : ""} selected`);
        } else {
          $counter.remove();
        }
      }
    }

    initializeTooltips() {
      // Add tooltips for complex fields
      const tooltips = {
        businessGoal:
          "What is the primary action you want visitors to take on your website?",
        designStyle:
          "This will influence colors, typography, and overall visual approach",
        contentStatus:
          "Do you have text, images, and other content ready for your website?",
      };

      Object.entries(tooltips).forEach(([id, text]) => {
        $(`#${id}`).parent().append(`
                    <div class="awb-tooltip" style="font-size: 0.75rem; color: rgba(255, 255, 255, 0.6); margin-top: 0.25rem;">
                        💡 ${text}
                    </div>
                `);
      });
    }

    async generateConcept() {
      try {
        this.isGenerating = true;

        // Hide form and show loading
        $("#awb-form").hide();
        $("#awb-loading").show();

        // Animate progress steps
        await this.animateProgressSteps();

        // Call backend
        const response = await this.callBackend();

        if (response.success) {
          this.displayResults(response.data);
          this.trackEvent("concept_generated", {
            industry: this.formData.industry,
            websiteType: this.formData.websiteType,
            featureCount: this.formData.features.length,
          });
        } else {
          this.showError(response.error || "Failed to generate concept");
        }
      } catch (error) {
        console.error("Error generating concept:", error);
        this.showError("An unexpected error occurred. Please try again.");
      } finally {
        this.isGenerating = false;
      }
    }

    async animateProgressSteps() {
      const $steps = $(".awb-step");
      const messages = [
        "Analyzing your business requirements...",
        "Researching industry best practices...",
        "Generating design concepts and wireframes...",
        "Creating color palettes and typography...",
        "Calculating costs and timelines...",
        "Finalizing recommendations...",
      ];

      for (let i = 0; i < $steps.length; i++) {
        await new Promise((resolve) => setTimeout(resolve, 800));

        const $step = $steps.eq(i);
        $step.addClass("active");

        // Update message if available
        if (messages[i]) {
          $step.find("span").text(messages[i]);
        }
      }
    }

    async callBackend() {
      const response = await $.ajax({
        url: awb_ajax.ajaxurl,
        method: "POST",
        data: {
          action: "awb_generate_concept",
          nonce: awb_ajax.nonce,
          form_data: JSON.stringify(this.formData),
        },
        timeout: 120000, // 2 minutes timeout
      });

      return JSON.parse(response);
    }

    displayResults(data) {
      $("#awb-loading").hide();

      const resultsHtml = this.buildResultsHTML(data);
      $("#awb-results").html(resultsHtml).show();

      // Bind result events
      this.bindResultEvents(data);

      // Animate results appearance
      this.animateResults();

      // Scroll to results
      $("#awb-results")[0].scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }

    buildResultsHTML(data) {
      const concept = data.concept || {};
      const colorScheme = data.colorScheme || {};
      const wireframe = data.wireframe || {};
      const features = data.features || {};
      const techSpecs = data.technical_specs || {};
      const costBreakdown = data.cost_breakdown || {};

      return `
                <div class="awb-results-header">
                    <div class="awb-success-icon">✓</div>
                    <h2 class="awb-results-title">Your AI-Generated Website Concept is Ready!</h2>
                    <p class="awb-results-subtitle">Complete with wireframes, cost estimates, and timeline projections</p>
                </div>

                <div class="awb-concept-card">
                    <div class="awb-concept-header">
                        <div>
                            <h3 class="awb-concept-title">${this.escapeHtml(
                              concept.title || "Your Business"
                            )}</h3>
                            <p class="awb-concept-tagline">"${this.escapeHtml(
                              concept.tagline || "Your success is our mission"
                            )}"</p>
                        </div>
                        <button class="awb-reset-btn" onclick="awbInstance.resetForm()" title="Generate New Concept">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                    
                    <p class="awb-concept-description">${this.escapeHtml(
                      concept.description ||
                        "A professional website designed to help your business grow and succeed online."
                    )}</p>

                    <div class="awb-metrics-grid">
                        <div class="awb-metric-card">
                            <div class="awb-metric-value" style="color: var(--awb-success)">${
                              concept.estimatedCost || "$5,000 - $8,000"
                            }</div>
                            <div class="awb-metric-label">Estimated Cost</div>
                        </div>
                        <div class="awb-metric-card">
                            <div class="awb-metric-value" style="color: var(--awb-secondary)">${
                              concept.timeline || "6-8 weeks"
                            }</div>
                            <div class="awb-metric-label">Timeline</div>
                        </div>
                        <div class="awb-metric-card">
                            <div class="awb-metric-value" style="color: var(--awb-primary)">${
                              concept.pages || "8"
                            }</div>
                            <div class="awb-metric-label">Pages</div>
                        </div>
                        <div class="awb-metric-card">
                            <div class="awb-metric-value" style="color: var(--awb-accent)">${
                              data.seoScore || "95"
                            }/100</div>
                            <div class="awb-metric-label">SEO Score</div>
                        </div>
                    </div>
                </div>

                ${this.buildWireframeSection(wireframe)}
                ${this.buildColorPaletteSection(colorScheme)}
                ${this.buildFeaturesSection(features)}
                ${this.buildTechnicalSpecsSection(techSpecs)}
                ${this.buildCostBreakdownSection(costBreakdown)}
                ${this.buildActionButtonsSection(data)}
            `;
    }

    buildWireframeSection(wireframe) {
      if (!wireframe.homepage || !wireframe.homepage.sections) {
        return this.buildBasicWireframe();
      }

      const sections = wireframe.homepage.sections;
      const sectionsHtml = sections
        .map(
          (section) => `
                <div class="awb-wireframe-section" 
                     style="min-height: ${Math.max(
                       section.height * 0.1,
                       40
                     )}px; background-color: ${section.color || "#374151"};"
                     title="${section.name} - ${
            section.priority || "normal"
          } priority">
                    <div style="font-weight: 600;">${section.name}</div>
                    ${
                      section.elements
                        ? `<div style="font-size: 0.75rem; opacity: 0.8;">${
                            Object.keys(section.elements).length
                          } elements</div>`
                        : ""
                    }
                </div>
            `
        )
        .join("");

      return `
                <div class="awb-content-section">
                    <h3>
                        <svg class="awb-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Website Structure & Layout
                    </h3>
                    <div class="awb-wireframe-preview">
                        ${sectionsHtml}
                    </div>
                    ${
                      wireframe.layout_rationale
                        ? `
                        <div style="margin-top: 1rem; color: rgba(255, 255, 255, 0.8); font-size: 0.875rem;">
                            <strong>Design Rationale:</strong> ${Object.values(
                              wireframe.layout_rationale
                            )
                              .slice(0, 2)
                              .join(". ")}.
                        </div>
                    `
                        : ""
                    }
                </div>
            `;
    }

    buildBasicWireframe() {
      const defaultSections = [
        { name: "Header", height: 80, color: "#1e293b" },
        { name: "Hero Section", height: 400, color: "#3b82f6" },
        { name: "Features", height: 300, color: "#f8fafc" },
        { name: "Services", height: 350, color: "#ffffff" },
        { name: "Testimonials", height: 250, color: "#f1f5f9" },
        { name: "Contact CTA", height: 200, color: "#6366f1" },
        { name: "Footer", height: 200, color: "#0f172a" },
      ];

      const sectionsHtml = defaultSections
        .map(
          (section) => `
                <div class="awb-wireframe-section" style="min-height: ${
                  section.height * 0.15
                }px; background-color: ${section.color};">
                    ${section.name}
                </div>
            `
        )
        .join("");

      return `
                <div class="awb-content-section">
                    <h3>
                        <svg class="awb-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Website Structure & Layout
                    </h3>
                    <div class="awb-wireframe-preview">
                        ${sectionsHtml}
                    </div>
                </div>
            `;
    }

    buildColorPaletteSection(colorScheme) {
      const colors = {
        primary: colorScheme.primary || "#3b82f6",
        secondary: colorScheme.secondary || "#8b5cf6",
        accent: colorScheme.accent || "#f59e0b",
        background: colorScheme.background || "#ffffff",
        text: colorScheme.text || "#1f2937",
      };

      const colorItems = Object.entries(colors)
        .map(
          ([name, color]) => `
                <div class="awb-color-item">
                    <div class="awb-color-swatch" style="background-color: ${color};" onclick="awbInstance.copyToClipboard('${color}')"></div>
                    <div class="awb-color-name">${name}</div>
                    <div class="awb-color-hex">${color}</div>
                </div>
            `
        )
        .join("");

      return `
                <div class="awb-content-section">
                    <h3>
                        <svg class="awb-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                        </svg>
                        Color Palette
                        <small style="font-size: 0.75rem; font-weight: normal; margin-left: 1rem; opacity: 0.7;">(Click to copy)</small>
                    </h3>
                    <div class="awb-color-palette">
                        ${colorItems}
                    </div>
                </div>
            `;
    }

    buildFeaturesSection(features) {
      const essentialFeatures = features.essential || [
        "Contact Forms",
        "Mobile Responsive",
        "SEO Optimization",
      ];
      const recommendedFeatures = features.recommended || [
        "Live Chat",
        "Analytics Dashboard",
        "Social Integration",
      ];
      const advancedFeatures = features.advanced || [
        "CRM Integration",
        "API Connectivity",
        "Advanced Security",
      ];

      return `
                <div class="awb-content-section">
                    <h3>
                        <svg class="awb-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Recommended Features for Your Business
                    </h3>
                    <div class="awb-feature-grid">
                        <div class="awb-feature-card" style="border-left-color: var(--awb-success);">
                            <h4 style="color: var(--awb-success);">✓ Essential Features</h4>
                            <p>${essentialFeatures.slice(0, 3).join(", ")}</p>
                        </div>
                        <div class="awb-feature-card" style="border-left-color: var(--awb-secondary);">
                            <h4 style="color: var(--awb-secondary);">⭐ Recommended Features</h4>
                            <p>${recommendedFeatures.slice(0, 3).join(", ")}</p>
                        </div>
                        <div class="awb-feature-card" style="border-left-color: var(--awb-primary);">
                            <h4 style="color: var(--awb-primary);">🚀 Advanced Features</h4>
                            <p>${advancedFeatures.slice(0, 3).join(", ")}</p>
                        </div>
                    </div>
                </div>
            `;
    }

    buildTechnicalSpecsSection(techSpecs) {
      if (!techSpecs || !techSpecs.hosting) return "";

      const hosting = techSpecs.hosting;

      return `
                <div class="awb-content-section">
                    <h3>
                        <svg class="awb-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                        </svg>
                        Technical Specifications
                    </h3>
                    <div class="awb-feature-grid">
                        <div class="awb-feature-card">
                            <h4>Hosting Requirements</h4>
                            <p>Storage: ${
                              hosting.min_storage || "10GB"
                            } | Bandwidth: ${
        hosting.bandwidth || "100GB/month"
      } | SSL Required: ${hosting.ssl_required ? "Yes" : "No"}</p>
                        </div>
                        <div class="awb-feature-card">
                            <h4>Development Timeline</h4>
                            <p>Estimated development time: ${
                              techSpecs.development_time || "4-6 weeks"
                            } | Maintenance level: ${
        techSpecs.maintenance_level || "Medium"
      }</p>
                        </div>
                        <div class="awb-feature-card">
                            <h4>Technology Stack</h4>
                            <p>CMS: ${
                              techSpecs.recommended_cms || "WordPress"
                            } | PHP: ${
        hosting.php_version || "8.0+"
      } | MySQL: ${hosting.mysql_version || "5.7+"}</p>
                        </div>
                    </div>
                </div>
            `;
    }

    buildCostBreakdownSection(costBreakdown) {
      if (!costBreakdown || !costBreakdown.breakdown) return "";

      const breakdown = costBreakdown.breakdown;
      const breakdownItems = Object.entries(breakdown)
        .map(
          ([item, cost]) => `
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="text-transform: capitalize;">${item.replace(
                      "_",
                      " "
                    )}:</span>
                    <span>${
                      typeof cost === "number" ? cost.toLocaleString() : cost
                    }</span>
                </div>
            `
        )
        .join("");

      return `
                <div class="awb-content-section">
                    <h3>
                        <svg class="awb-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                        Cost Breakdown & Investment
                    </h3>
                    <div class="awb-feature-grid">
                        <div class="awb-feature-card">
                            <h4>Project Breakdown</h4>
                            <div style="font-size: 0.875rem; color: rgba(255, 255, 255, 0.8);">
                                ${breakdownItems}
                                <hr style="margin: 1rem 0; border-color: rgba(255, 255, 255, 0.2);">
                                <div style="display: flex; justify-content: space-between; font-weight: 600; color: var(--awb-accent);">
                                    <span>Total Range:</span>
                                    <span>${
                                      costBreakdown.range || "$5,000 - $8,000"
                                    }</span>
                                </div>
                            </div>
                        </div>
                        <div class="awb-feature-card">
                            <h4>Payment Schedule</h4>
                            <div style="font-size: 0.875rem; color: rgba(255, 255, 255, 0.8);">
                                ${
                                  costBreakdown.payment_schedule
                                    ? Object.entries(
                                        costBreakdown.payment_schedule
                                      )
                                        .map(
                                          ([phase, amount]) => `
                                    <div style="margin-bottom: 0.5rem;">${phase.replace(
                                      "_",
                                      " "
                                    )}: ${amount}</div>
                                `
                                        )
                                        .join("")
                                    : "Flexible payment options available"
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
    }

    buildActionButtonsSection(data) {
      return `
                <div class="awb-action-buttons">
                    <button class="awb-action-btn awb-action-btn-primary" onclick="awbInstance.downloadConcept()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Concept
                    </button>
                    
                    <button class="awb-action-btn awb-action-btn-secondary" onclick="awbInstance.generateDemo()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Build Live Demo
                    </button>
                    
                    <button class="awb-action-btn awb-action-btn-tertiary" onclick="awbInstance.requestQuote()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Request Quote
                    </button>
                </div>
            `;
    }

    bindResultEvents(data) {
      // Store data for later use
      this.currentConcept = data;

      // Add click handlers for interactive elements
      $(".awb-wireframe-section").on("click", function () {
        $(this).toggleClass("highlighted");
      });
    }

    animateResults() {
      // Animate sections appearing
      $(".awb-content-section").each((index, element) => {
        setTimeout(() => {
          $(element)
            .css({
              opacity: "0",
              transform: "translateY(20px)",
            })
            .animate(
              {
                opacity: "1",
                transform: "translateY(0)",
              },
              500
            );
        }, index * 200);
      });
    }

    async generateDemo() {
      if (!this.currentConcept) {
        this.showError("No concept data available for demo generation");
        return;
      }

      try {
        // Show loading state
        const $btn = $('button:contains("Build Live Demo")');
        const originalHtml = $btn.html();
        $btn
          .html(
            `
                    <div class="awb-spinner" style="width: 20px; height: 20px; margin: 0; border-width: 2px;"></div>
                    Generating Demo...
                `
          )
          .prop("disabled", true);

        const response = await $.ajax({
          url: awb_ajax.ajaxurl,
          method: "POST",
          data: {
            action: "awb_generate_demo",
            nonce: awb_ajax.nonce,
            concept_data: JSON.stringify(this.currentConcept),
          },
          timeout: 60000,
        });

        const result = JSON.parse(response);

        if (result.success) {
          // Open demo in new tab
          window.open(result.demo_url, "_blank");

          // Update button
          $btn
            .html(
              `
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6m0-2h6v6"/>
                        </svg>
                        View Live Demo
                    `
            )
            .prop("disabled", false);

          // Store demo URL for future clicks
          $btn.attr("onclick", `window.open('${result.demo_url}', '_blank')`);

          this.trackEvent("demo_generated", {
            demo_id: result.demo_id,
            business_type: this.formData.businessType,
          });
        } else {
          throw new Error(result.error || "Failed to generate demo");
        }
      } catch (error) {
        console.error("Demo generation error:", error);
        this.showError("Failed to generate demo. Please try again.");

        // Restore button
        $('button:contains("Generating Demo")')
          .html(originalHtml)
          .prop("disabled", false);
      }
    }

    downloadConcept() {
      if (!this.currentConcept) {
        this.showError("No concept data available for download");
        return;
      }

      // Create downloadable content
      const conceptText = this.formatConceptForDownload(this.currentConcept);
      const blob = new Blob([conceptText], { type: "text/plain" });
      const url = window.URL.createObjectURL(blob);

      // Create download link
      const a = document.createElement("a");
      a.href = url;
      a.download = `${this.formData.businessType.replace(
        /[^a-z0-9]/gi,
        "_"
      )}_website_concept.txt`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);

      this.trackEvent("concept_downloaded", {
        business_type: this.formData.businessType,
      });
    }

    formatConceptForDownload(data) {
      const concept = data.concept || {};
      const colorScheme = data.colorScheme || {};
      const features = data.features || {};

      return `
AI-GENERATED WEBSITE CONCEPT
============================

Business: ${concept.title || "N/A"}
Tagline: ${concept.tagline || "N/A"}
Description: ${concept.description || "N/A"}

PROJECT OVERVIEW
===============
Estimated Cost: ${concept.estimatedCost || "N/A"}
Timeline: ${concept.timeline || "N/A"}
Number of Pages: ${concept.pages || "N/A"}
SEO Score: ${data.seoScore || "N/A"}/100

COLOR SCHEME
===========
Primary: ${colorScheme.primary || "N/A"}
Secondary: ${colorScheme.secondary || "N/A"}
Accent: ${colorScheme.accent || "N/A"}
Background: ${colorScheme.background || "N/A"}
Text: ${colorScheme.text || "N/A"}

RECOMMENDED FEATURES
==================
Essential: ${features.essential ? features.essential.join(", ") : "N/A"}
Recommended: ${features.recommended ? features.recommended.join(", ") : "N/A"}
Advanced: ${features.advanced ? features.advanced.join(", ") : "N/A"}

FORM DATA SUBMITTED
==================
Industry: ${this.formData.industry}
Website Type: ${this.formData.websiteType}
Design Style: ${this.formData.designStyle}
Target Audience: ${this.formData.targetAudience}
Business Goal: ${this.formData.businessGoal}
Budget: ${this.formData.budget}
Selected Features: ${this.formData.features.join(", ")}

Generated on: ${new Date().toLocaleDateString()}
Generated by: AI Web Builder Plugin
Contact: https://sawahsolutions.com
            `;
    }

    requestQuote() {
      // Track the event
      this.trackEvent("quote_requested", {
        business_type: this.formData.businessType,
        industry: this.formData.industry,
      });

      // Open contact page
      window.open("https://sawahsolutions.com/get-started", "_blank");
    }

    copyToClipboard(text) {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
          this.showNotification(`Copied ${text} to clipboard!`);
        });
      } else {
        // Fallback for older browsers
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("copy");
        document.body.removeChild(textArea);
        this.showNotification(`Copied ${text} to clipboard!`);
      }
    }

    resetForm() {
      // Reset form data
      this.formData = {
        businessType: "",
        industry: "",
        companySize: "",
        targetAudience: "",
        businessGoal: "",
        budget: "",
        businessDescription: "",
        pageCount: "",
        contentStatus: "",
        timeline: "",
        currentWebsite: "",
        existingWebsite: "",
        competitors: "",
        websiteType: "",
        designStyle: "",
        existingBranding: "",
        colorPreference: "",
        designInspiration: "",
        additionalRequirements: "",
        features: [],
      };

      // Reset current concept
      this.currentConcept = null;
      this.isGenerating = false;

      // Reset form inputs
      $("#awb-form")[0].reset();

      // Reset selections
      $(".awb-option-btn, .awb-style-btn, .awb-feature-btn").removeClass(
        "selected"
      );

      // Reset progress steps
      $(".awb-step").removeClass("active");

      // Remove feature counter
      $(".awb-feature-counter").remove();

      // Show form, hide results and loading
      $("#awb-form").show();
      $("#awb-loading, #awb-results").hide();

      // Scroll to top
      $("html, body").animate(
        {
          scrollTop: $("#ai-web-builder-container").offset().top - 100,
        },
        500
      );

      this.validateForm();

      this.trackEvent("form_reset", {});
    }

    showError(message) {
      // Hide loading
      $("#awb-loading").hide();

      // Show form
      $("#awb-form").show();

      // Show error notification
      this.showNotification(message, "error");
    }

    showNotification(message, type = "success") {
      const bgColor =
        type === "error" ? "var(--awb-error)" : "var(--awb-success)";

      const notification = $(`
                <div class="awb-notification" style="
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${bgColor};
                    color: white;
                    padding: 1rem 1.5rem;
                    border-radius: var(--awb-radius);
                    box-shadow: var(--awb-shadow-lg);
                    z-index: 10000;
                    max-width: 300px;
                    animation: slideInRight 0.3s ease;
                ">
                    ${message}
                    <button onclick="$(this).parent().remove()" style="
                        background: none;
                        border: none;
                        color: white;
                        float: right;
                        margin-left: 1rem;
                        cursor: pointer;
                        font-size: 1.2rem;
                    ">&times;</button>
                </div>
            `);

      $("body").append(notification);

      // Auto remove after 5 seconds
      setTimeout(() => {
        notification.fadeOut(() => notification.remove());
      }, 5000);
    }

    trackEvent(eventName, data = {}) {
      // Simple event tracking - can be extended with analytics
      if (typeof gtag !== "undefined") {
        gtag("event", eventName, {
          custom_parameter: JSON.stringify(data),
        });
      }

      console.log("AWB Event:", eventName, data);
    }

    escapeHtml(text) {
      if (!text) return "";
      const div = document.createElement("div");
      div.textContent = text;
      return div.innerHTML;
    }

    // Utility method for form auto-save
    autoSaveForm() {
      const formState = {
        ...this.formData,
        timestamp: Date.now(),
      };

      try {
        localStorage.setItem("awb_form_data", JSON.stringify(formState));
      } catch (e) {
        console.warn("Could not save form data to localStorage:", e);
      }
    }

    // Restore form from auto-save
    restoreForm() {
      try {
        const saved = localStorage.getItem("awb_form_data");
        if (saved) {
          const formState = JSON.parse(saved);
          const timeDiff = Date.now() - formState.timestamp;

          // Only restore if saved within last hour
          if (timeDiff < 3600000) {
            // Restore text inputs
            Object.keys(this.formData).forEach((key) => {
              if (formState[key] && typeof formState[key] === "string") {
                $(`#${key}`).val(formState[key]);
                this.formData[key] = formState[key];
              }
            });

            // Restore selections
            if (formState.websiteType) {
              $(
                `.awb-option-btn[data-value="${formState.websiteType}"]`
              ).addClass("selected");
            }
            if (formState.designStyle) {
              $(
                `.awb-style-btn[data-value="${formState.designStyle}"]`
              ).addClass("selected");
            }
            if (formState.features && Array.isArray(formState.features)) {
              formState.features.forEach((feature) => {
                $(`.awb-feature-btn[data-value="${feature}"]`).addClass(
                  "selected"
                );
              });
              this.formData.features = [...formState.features];
            }

            this.updateFeatureCount();
            this.validateForm();

            this.showNotification("Previous form data restored", "success");
          }
        }
      } catch (e) {
        console.warn("Could not restore form data:", e);
      }
    }

    // Clear auto-saved data
    clearAutoSave() {
      try {
        localStorage.removeItem("awb_form_data");
      } catch (e) {
        console.warn("Could not clear saved form data:", e);
      }
    }
  }

  // Initialize when document is ready
  $(document).ready(function () {
    if ($("#ai-web-builder-container").length) {
      window.awbInstance = new AIWebBuilder();

      // Set up auto-save every 30 seconds
      setInterval(() => {
        if (
          window.awbInstance &&
          (window.awbInstance.formData.businessType ||
            window.awbInstance.formData.industry)
        ) {
          window.awbInstance.autoSaveForm();
        }
      }, 30000);

      // Restore form on page load
      setTimeout(() => {
        window.awbInstance.restoreForm();
      }, 100);

      // Clear auto-save when form is successfully submitted
      $(document).on("awb:concept_generated", () => {
        window.awbInstance.clearAutoSave();
      });
    }
  });

  // Add CSS animations
  const style = document.createElement("style");
  style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .awb-content-section {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        .awb-btn-disabled {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
            transform: none !important;
        }
        
        .awb-wireframe-section.highlighted {
            background-color: rgba(139, 92, 246, 0.3) !important;
            border: 2px solid var(--awb-primary);
        }
        
        .awb-color-swatch {
            cursor: pointer;
            transition: var(--awb-transition);
        }
        
        .awb-color-swatch:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .awb-notification {
            animation: slideInRight 0.3s ease;
        }
    `;
  document.head.appendChild(style);
})(jQuery);
