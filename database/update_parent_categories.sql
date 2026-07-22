-- ============================================================
-- Update parent categories with base_price + attributes_schema
-- Also reset sub-category base_price to 0 (unused)
-- ============================================================

-- Reset sub-categories (base_price not used in sub-categories)
UPDATE categories SET base_price = 0, attributes_schema = NULL WHERE parent_id IS NOT NULL;

-- JAKET (id 25) — base_price = 175000
UPDATE categories SET
  base_price = 175000,
  attributes_schema = '[{"id":"tipe_jaket","name":"Tipe Jaket","type":"select","required":true,"options":[{"value":"Training","price_modifier":0},{"value":"Kasual & Komunitas","price_modifier":0}]},{"id":"furing","name":"Furing","type":"select","required":true,"options":[{"value":"Non Furing","price_modifier":0},{"value":"Furing","price_modifier":20000}]}]'
WHERE id = 25;

-- BAWAHAN (id 26) — base_price = 70000
UPDATE categories SET
  base_price = 70000,
  attributes_schema = '[{"id":"tipe_bawahan","name":"Tipe Bawahan","type":"select","required":true,"options":[{"value":"Training Pendek","price_modifier":0},{"value":"Training Panjang","price_modifier":40000},{"value":"Skort","price_modifier":95000}]}]'
WHERE id = 26;

-- KEMEJA & PAKAIAN DINAS (id 27) — base_price = 145000
UPDATE categories SET
  base_price = 145000,
  attributes_schema = '[{"id":"tipe_kemeja","name":"Tipe Kemeja","type":"select","required":true,"options":[{"value":"PDH & Workshirt","price_modifier":0},{"value":"PDL Reguler","price_modifier":10000},{"value":"PDL Tactical","price_modifier":105000}]},{"id":"bahan","name":"Bahan","type":"select","required":true,"options":[{"value":"American","price_modifier":0},{"value":"Nagata","price_modifier":10000},{"value":"Ripstop","price_modifier":10000}]}]'
WHERE id = 27;

-- KAOS & POLO (id 28) — base_price = 70000
UPDATE categories SET
  base_price = 70000,
  attributes_schema = '[{"id":"tipe_kaos","name":"Tipe Kaos / Polo","type":"select","required":true,"options":[{"value":"Cotton 30s","price_modifier":0},{"value":"Cotton 24s","price_modifier":5000},{"value":"Cotton 20s","price_modifier":20000},{"value":"Cotton 16s","price_modifier":30000},{"value":"Polo GVC","price_modifier":25000},{"value":"Polo Cotton","price_modifier":30000}]}]'
WHERE id = 28;

-- AKSESORIS (id 29) — base_price = 14000
UPDATE categories SET
  base_price = 14000,
  attributes_schema = '[{"id":"tipe_aksesoris","name":"Tipe Aksesoris","type":"select","required":true,"options":[{"value":"Lanyard","price_modifier":0},{"value":"Scarf","price_modifier":11000},{"value":"Tas Serut","price_modifier":26000},{"value":"Topi","price_modifier":0},{"value":"Totebag","price_modifier":0}]},{"id":"tipe_cetak","name":"Tipe Cetak","type":"select","required":true,"depends_on":{"attribute_id":"tipe_aksesoris","value":"Lanyard"},"options":[{"value":"Print 1 Sisi","price_modifier":0},{"value":"Print 2 Sisi","price_modifier":1000}]},{"id":"card_holder","name":"Card Holder","type":"select","required":false,"depends_on":{"attribute_id":"tipe_aksesoris","value":"Lanyard"},"options":[{"value":"PVC","price_modifier":0}]},{"id":"ukuran_scarf","name":"Ukuran","type":"select","required":true,"depends_on":{"attribute_id":"tipe_aksesoris","value":"Scarf"},"options":[{"value":"55 x 55","price_modifier":0},{"value":"75 x 75","price_modifier":20000}]},{"id":"bahan_scarf","name":"Bahan","type":"select","required":true,"depends_on":{"attribute_id":"tipe_aksesoris","value":"Scarf"},"options":[{"value":"Micro Poly","price_modifier":0}]},{"id":"bahan_tas_serut","name":"Bahan","type":"select","required":true,"depends_on":{"attribute_id":"tipe_aksesoris","value":"Tas Serut"},"options":[{"value":"Polyester","price_modifier":0}]},{"id":"ukuran_tas_serut","name":"Ukuran","type":"select","required":true,"depends_on":{"attribute_id":"tipe_aksesoris","value":"Tas Serut"},"options":[{"value":"45 x 33","price_modifier":0}]}]'
WHERE id = 29;
