# Improved Blade Localization Audit Report

**Directory:** tools
**Generated:** 2025-07-20 03:52:37
**Files processed:** 1

## 📝 Localizable Texts Found (50)

- `,
        density: 7.85, // g/cm³
        price: 25000, // VND per kg
        grades: [`
- `).value) || 0;
    
    if (!materialType || !length || !width || !thickness) {
        return;
    }
    
    const material = materialProperties[materialType];
    if (!material) return;
    
    // Calculate volume in cm³
    const volume = (length * width * thickness) / 1000; // mm³ to cm³
    
    // Calculate weight in kg
    const weight = (volume * material.density) / 1000; // g to kg
    
    // Apply waste percentage
    const totalWeight = weight * (1 + wastePercentage / 100) * quantity;
    
    // Calculate costs
    const materialCost = totalWeight * material.price;
    const totalLaborCost = laborCost * processingTime * quantity;
    const totalCost = materialCost + totalLaborCost;
    
    // Display results
    const resultsDiv = document.getElementById(`
- `Material Cost Calculator`
- `Save Calculation`
- `Export Results`
- `History`
- `Material Calculation Parameters`
- `Material Type`
- `Select Material`
- `Carbon Steel`
- `Stainless Steel`
- `Aluminum`
- `Copper`
- `Brass`
- `Titanium`
- `ABS Plastic`
- `Select Grade`
- `Quantity`
- `Shape Type`
- `Form Type`
- `Raw Material`
- `Machined`
- `Fabricated`
- `Finished Product`
- `Additional Parameters`
- `Currency`
- `Unit System`
- `Calculate Material Cost`
- `Calculation Results`
- `Material Properties`
- `Quick Calculations`
- `Steel Sheet`
- `Steel Pipe`
- `Aluminum Block`
- `Recent Calculations`
- `Date`
- `Material`
- `Dimensions`
- `Total Cost`
- `Actions`
- `No calculations yet`
- `Density`
- `Price`
- `Tensile Strength`
- `Yield Strength`
- `Volume`
- `Weight`
- `Material Cost`
- `Labor Cost`
- `Cost per Unit`

## 🔑 Existing Translation Keys (0)


## 🎯 Priority Fixes (3)

### Text: `,
        density: 7.85, // g/cm³
        price: 25000, // VND per kg
        grades: [` (Priority: 10)
- **Key:** `tools._density_785_gcm_price_25000_v`
- **Helper:** `t_content('tools._density_785_gcm_price_25000_v')`
- **Directive:** `@content('tools._density_785_gcm_price_25000_v')`

### Text: `).value) || 0;
    
    if (!materialType || !length || !width || !thickness) {
        return;
    }
    
    const material = materialProperties[materialType];
    if (!material) return;
    
    // Calculate volume in cm³
    const volume = (length * width * thickness) / 1000; // mm³ to cm³
    
    // Calculate weight in kg
    const weight = (volume * material.density) / 1000; // g to kg
    
    // Apply waste percentage
    const totalWeight = weight * (1 + wastePercentage / 100) * quantity;
    
    // Calculate costs
    const materialCost = totalWeight * material.price;
    const totalLaborCost = laborCost * processingTime * quantity;
    const totalCost = materialCost + totalLaborCost;
    
    // Display results
    const resultsDiv = document.getElementById(` (Priority: 10)
- **Key:** `tools.value_0_if_materialtype_length`
- **Helper:** `t_content('tools.value_0_if_materialtype_length')`
- **Directive:** `@content('tools.value_0_if_materialtype_length')`

### Text: `Additional Parameters` (Priority: 0)
- **Key:** `tools.additional_parameters`
- **Helper:** `t_content('tools.additional_parameters')`
- **Directive:** `@content('tools.additional_parameters')`

