import { addFilter } from "@wordpress/hooks";
import { __ } from "@wordpress/i18n";


addFilter(
  'woocommerce_admin_variations_report_advanced_filters',
  'plugin-domain',
  (report) => {
    // Get dynamic states from wcSettings or fallback to an empty array
    const dynamicStates = (wcSettings.shippingStates || []).map((state) => ({
      value: state.value,
      label: state.label,
    }));

    return {
      ...report,
      filters: {
        ...report.filters,
        shipping_states: {
          allowMultiple: true,
          labels: {
            add: 'Shipping States',
            remove: 'Remove shipping states filter',
            rule: 'Select a match for shipping states',
            title:
              '{{title}}shipping state{{/title}} {{rule /}} {{filter /}}',
            filter: 'Select one or more states',
          },
          rules: [
            {
              value: 'is',
              label: 'is',
            },
            

          ],
          input: {
            component: 'SelectControl',
         
            options: dynamicStates, // Use dynamic states for options
          },
        },
      },
    };
  }
);



const addVariationFilters = (filters) => {

  return [
    {
      label: __("Order status", "dev-blog-example"),
      staticParams: [],
      param: "order_status",
      showFilters: () => true,
      filters: [
        { label: __('All', 'dev-blog-example'), value: 'all' },
        { label: __('Shipped', 'dev-blog-example'), value: 'completed' },
     
      ],
    },
    ...filters,
  ];
};



addFilter(
  "woocommerce_admin_variations_report_filters",
  "dev-blog-example",
  addVariationFilters
);


const ssaddVariationFilters = (filters) => {
  return [
    {
      label: __("Province", "dev-blog-example"),
      staticParams: [],
      param: "state",
      showFilters: () => true,
      filters: [
        { label: __('All', 'dev-blog-example'), value: 'all' },
        ...(wcSettings.shippingStates || [])
      ],
    
    },
    ...filters,
  ];
};

// addFilter(
//   "woocommerce_admin_variations_report_filters",
//   "dev-blog-example",
//   ssaddVariationFilters
// );

