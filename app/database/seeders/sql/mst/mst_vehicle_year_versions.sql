INSERT INTO mst_vehicle_year_versions (
    vehicle_id, year_model, displacement_cc, drive_type, 
    fuel_efficiency_from, fuel_efficiency_to, max_power_kw, 
    transmission_type, weight_kg, price_range_from, price_range_to, 
    is_latest, created_at, updated_at, deleted_at 
) VALUES
(1, 2011, 1797, 'FF', 26.6, 30.4, 100, 'CVT', 1460, 3550000, 4880000, 0, NOW(), NOW(), NULL),
(2, 2014, 1797, 'FF', 26.6, 30.4, 100, 'CVT', 1460, 3550000, 4880000, 0, NOW(), NOW(), NULL),
(3, 2017, 1797, 'FF', 23.9, 26.6, 100, 'CVT', 1440, 3860000, 4880000, 0, NOW(), NOW(), NULL);