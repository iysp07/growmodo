<?php


defined( 'ABSPATH' ) || exit;

add_action(
	'after_switch_theme',
	function () {
		if ( ! get_option( 'gm_seeded' ) ) {
			gm_seed_demo();
		}
	},
	20
);


function gm_seed_image( $file, $title ) {
	static $cache = array();
	if ( isset( $cache[ $file ] ) ) {
		return $cache[ $file ];
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$src = GM_DIR . '/assets/images/' . $file;
	if ( ! file_exists( $src ) ) {
		return 0;
	}
	$up   = wp_upload_dir();
	$name = wp_unique_filename( $up['path'], 'growmodo-' . $file );
	$dest = trailingslashit( $up['path'] ) . $name;
	copy( $src, $dest );
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/webp',
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$dest
	);
	if ( $id && ! is_wp_error( $id ) ) {
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $dest ) );
		update_post_meta( $id, '_wp_attachment_image_alt', $title );
	}
	$cache[ $file ] = (int) $id;
	return $cache[ $file ];
}


function gm_seed_post( $type, $title, $args = array(), $meta = array(), $terms = array(), $thumb = '' ) {
	$existing = get_posts( array( 'post_type' => $type, 'title' => $title, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( $existing ) {
		return $existing[0];
	}
	$id = wp_insert_post( array_merge( array( 'post_type' => $type, 'post_title' => $title, 'post_status' => 'publish' ), $args ) );
	if ( ! $id || is_wp_error( $id ) ) {
		return 0;
	}
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, '_gm_' . $k, $v );
	}
	foreach ( $terms as $tax => $t ) {
		wp_set_object_terms( $id, (array) $t, $tax );
	}
	if ( $thumb ) {
		set_post_thumbnail( $id, gm_seed_image( $thumb, $title ) );
	}
	return $id;
}

function gm_seed_demo( $force = false ) {
	if ( get_option( 'gm_seeded' ) && ! $force ) {
		return;
	}
	@set_time_limit( 300 ); 

	
	update_option( 'blogname', 'Growmodo' );
	update_option( 'blogdescription', 'Discover your dream property with Estatein' );
	update_option( 'permalink_structure', '/%postname%/' );

	
	$pages = array(
		'home'     => array( 'Home', 'home', '' ),
		'about'    => array( 'Our Journey', 'about-us', 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary. Over the years, we\'ve expanded our reach, forged valuable partnerships, and gained the trust of countless clients.' ),
		'props'    => array( 'Find Your Dream Property', 'properties', 'Welcome to Estatein, where your dream property awaits in every corner of our beautiful world. Explore our curated selection of properties, each offering a unique story and a chance to redefine your life. With categories to suit every dreamer, your journey' ),
		'services' => array( 'Elevate Your Real Estate Experience', 'services', 'Welcome to Estatein, where your real estate aspirations meet expert guidance. Explore our comprehensive range of services, each designed to cater to your unique needs and dreams.' ),
		'contact'  => array( 'Get in Touch with Estatein', 'contact-us', "Welcome to Estatein's Contact Us page. We're here to assist you with any inquiries, requests, or feedback you may have. Whether you're looking to buy or sell a property, explore investment opportunities, or simply want to connect, we're just a message away. Reach out to us, and let's start a conversation." ),
		'terms'    => array( 'Terms & Conditions', 'terms-conditions', "By using this website you agree to these terms. This is placeholder legal text – replace it with your own Terms of Use and Privacy Policy before launch.\n\nWe collect only the information you submit through our forms (name, email, phone and message) and use it solely to respond to your inquiry. We never sell your data." ),
	);
	$ids = array();
	foreach ( $pages as $k => $p ) {
		$existing = get_page_by_path( $p[1] );
		if ( $existing ) {
			$ids[ $k ] = $existing->ID;
			wp_update_post( array( 'ID' => $existing->ID, 'post_title' => $p[0], 'post_content' => $p[2], 'post_status' => 'publish' ) );
			continue;
		}
		$ids[ $k ] = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $p[0], 'post_name' => $p[1], 'post_content' => $p[2] ) );
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['home'] );

	
	$menu = wp_get_nav_menu_object( 'Primary' );
	if ( ! $menu ) {
		$mid = wp_create_nav_menu( 'Primary' );
		foreach ( array( 'Home' => 'home', 'About Us' => 'about', 'Properties' => 'props', 'Services' => 'services', 'Contact Us' => 'contact' ) as $label => $k ) {
			wp_update_nav_menu_item( $mid, 0, array( 'menu-item-title' => $label, 'menu-item-object' => 'page', 'menu-item-object-id' => $ids[ $k ], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		}
		$locations            = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary'] = $mid;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	
	foreach ( array( 'California', 'New York', 'North Carolina', 'Nevada', 'Florida', 'Texas' ) as $t ) {
		wp_insert_term( $t, 'property_location' );
	}
	foreach ( array( 'Villa', 'Apartment', 'Cottage', 'Townhouse' ) as $t ) {
		wp_insert_term( $t, 'property_type' );
	}
	foreach ( array( 'Regional', 'International' ) as $t ) {
		wp_insert_term( $t, 'office_type' );
	}
	wp_insert_term( 'Our Values', 'about_group', array( 'slug' => 'values', 'description' => 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary.' ) );
	wp_insert_term( 'Our Achievements', 'about_group', array( 'slug' => 'achievements', 'description' => 'Our story is one of continuous growth and evolution. We started as a small team with big dreams, determined to create a real estate platform that transcended the ordinary.' ) );
	wp_insert_term( 'Navigating the Estatein Experience', 'about_group', array( 'slug' => 'steps', 'description' => "At Estatein, we've designed a straightforward process to help you find and purchase your dream property with ease. Here's a step-by-step guide to how it all works." ) );

	$groups = array(
		'selling'    => array( 'Unlock Property Value', 'Selling your property should be a rewarding experience, and at Estatein, we make sure it is. Our Property Selling Service is designed to maximize the value of your property, ensuring you get the best deal possible. Explore the categories below to see how we can help you at every step of your selling journey', 'Unlock the Value of Your Property Today', 'Ready to unlock the true value of your property? Explore our Property Selling Service categories and let us help you achieve the best deal possible for your valuable asset.' ),
		'management' => array( 'Effortless Property Management', "Owning a property should be a pleasure, not a hassle. Estatein's Property Management Service takes the stress out of property ownership, offering comprehensive solutions tailored to your needs. Explore the categories below to see how we can make property management effortless for you", 'Experience Effortless Property Management', 'Ready to experience hassle-free property management? Explore our Property Management Service categories and let us handle the complexities while you enjoy the benefits of property ownership.' ),
		'investment' => array( 'Smart Investments, Informed Decisions', "Building a real estate portfolio requires a strategic approach. Estatein's Investment Advisory Service empowers you to make smart investments and informed decisions.", 'Unlock Your Investment Potential', 'Explore our Property Management Service categories and let us handle the complexities while you enjoy the benefits of property ownership.' ),
	);
	foreach ( $groups as $slug => $g ) {
		$r = wp_insert_term( $g[0], 'service_group', array( 'slug' => $slug, 'description' => $g[1] ) );
		$tid = is_wp_error( $r ) ? ( $r->get_error_data( 'term_exists' ) ?: 0 ) : $r['term_id'];
		if ( $tid ) {
			update_term_meta( $tid, 'cta_title', $g[2] );
			update_term_meta( $tid, 'cta_text', $g[3] );
		}
	}

	
	$gal_all = array();
	for ( $i = 1; $i <= 9; $i++ ) {
		$gal_all[] = gm_seed_image( "gal-$i.webp", "Seaside Serenity Villa photo $i" );
	}
	$props = array(
		array( 'Seaside Serenity Villa', 'Wake up to the soothing melody of waves. This beachfront villa offers...', 'Discover your own piece of paradise with the Seaside Serenity Villa. With an open floor plan, breathtaking ocean views from every room, and direct access to a pristine sandy beach, this property is the epitome of coastal living.', 'prop-seaside.webp', array( 'tagline' => 'Coastal Escapes - Where Waves Beckon', 'location_text' => 'Malibu, California', 'price' => 1250000, 'bedrooms' => 4, 'bathrooms' => 3, 'area' => 2500, 'year' => 2019 ), 'California', 'Villa', $gal_all ),
		array( 'Metropolitan Haven', 'Immerse yourself in the energy of the city. This modern apartment in the heart...', 'A chic and fully-furnished 2-bedroom apartment with panoramic city views, floor-to-ceiling windows and a short walk to restaurants, galleries and transit.', 'prop-metropolitan.webp', array( 'tagline' => 'Urban Oasis - Life in the Heart of the City', 'location_text' => 'Manhattan, New York', 'price' => 650000, 'bedrooms' => 2, 'bathrooms' => 2, 'area' => 1400, 'year' => 2021 ), 'New York', 'Apartment', array_slice( $gal_all, 4, 5 ) ),
		array( 'Rustic Retreat Cottage', 'Find tranquility in the countryside. This charming cottage is nestled amidst rolling hills...', 'An elegant 3-bedroom, 3-bathroom retreat in a gated community, surrounded by rolling hills and mature trees, with a wraparound porch made for slow mornings.', 'prop-rustic.webp', array( 'tagline' => "Countryside Charm - Escape to Nature's Embrace", 'location_text' => 'Asheville, North Carolina', 'price' => 350000, 'bedrooms' => 3, 'bathrooms' => 3, 'area' => 1800, 'year' => 2015 ), 'North Carolina', 'Cottage', array_slice( $gal_all, 2, 5 ) ),
		array( 'Lakeside Modern Residence', 'Mountain air, mirror-calm water and a deck built for sunsets. Welcome to lakeside living...', 'A five-bedroom contemporary residence on the shore of Lake Tahoe with a private dock, an open-plan great room and a chef’s kitchen.', 'gal-4.webp', array( 'tagline' => 'Lakeside Living - Calm Waters, Big Views', 'location_text' => 'Lake Tahoe, Nevada', 'price' => 890000, 'bedrooms' => 5, 'bathrooms' => 4, 'area' => 3800, 'year' => 2022 ), 'Nevada', 'Villa', array_slice( $gal_all, 3, 6 ) ),
		array( 'Skyline Penthouse', 'Live above it all. This penthouse pairs sweeping skyline views with resort-style amenities...', 'A light-filled three-bedroom penthouse with a private terrace, concierge service, a rooftop pool and 24-hour security.', 'gal-6.webp', array( 'tagline' => 'Sky-High Style - Penthouse Living', 'location_text' => 'Miami, Florida', 'price' => 1850000, 'bedrooms' => 3, 'bathrooms' => 3, 'area' => 2900, 'year' => 2023 ), 'Florida', 'Apartment', array_slice( $gal_all, 4, 5 ) ),
		array( 'Hillcrest Family Home', 'Room to grow in a friendly, tree-lined neighborhood close to top-rated schools...', 'A bright four-bedroom townhouse with a fenced yard, finished basement and a two-car garage, ideal for growing families.', 'gal-8.webp', array( 'tagline' => 'Family First - Space for Every Moment', 'location_text' => 'Austin, Texas', 'price' => 475000, 'bedrooms' => 4, 'bathrooms' => 3, 'area' => 2200, 'year' => 2018 ), 'Texas', 'Townhouse', array_slice( $gal_all, 5, 4 ) ),
	);
	foreach ( $props as $i => $p ) {
		$meta = $p[4] + array(
			'features'         => "Expansive terrace for outdoor entertaining\nGourmet kitchen with top-of-the-line appliances\nPrivate access for morning strolls and sunset views\nMaster suite with a spa-inspired bathroom and balcony\nPrivate garage and ample storage space",
			'fees'             => "Property Transfer Tax | \$25,000 | Based on the sale price and local regulations\nLegal Fees | \$3,000 | Approximate cost for legal services, including title transfer\nHome Inspection | \$500 | Recommended for due diligence\nProperty Insurance | \$1,200 | Annual cost for comprehensive property insurance\nMortgage Fees | Varies | If applicable, consult with your lender for specific details",
			'monthly_costs'    => "Property Taxes | \$1,250 | Approximate monthly property tax based on the sale price and local rates\nHomeowners' Association Fee | \$300 | Monthly fee for common area maintenance and security",
			'monthly_expenses' => "Property Taxes | \$1,250\nHomeowners' Association Fee | \$300\nMortgage Payment | Varies based on terms and interest rate | If applicable\nProperty Insurance | \$100 | Approximate monthly cost",
			'down_pct'         => 20,
			'gallery'          => implode( ',', array_filter( $p[7] ) ),
		);
		gm_seed_post( 'property', $p[0], array( 'post_excerpt' => $p[1], 'post_content' => $p[2], 'post_date' => gmdate( 'Y-m-d H:i:s', time() - $i * 3600 ) ), $meta, array( 'property_location' => $p[5], 'property_type' => $p[6] ), $p[3] );
	}

	
	$tests = array(
		array( 'Exceptional Service!', "Our experience with Estatein was outstanding. Their team's dedication and professionalism made finding our dream home a breeze. Highly recommended!", 'Wade Warren', 'USA, California', 'avatar-wade.webp' ),
		array( 'Efficient and Reliable', "Estatein provided us with top-notch service. They helped us sell our property quickly and at a great price. We couldn't be happier with the results.", 'Emelie Thomson', 'USA, Florida', 'avatar-emelie.webp' ),
		array( 'Trusted Advisors', 'The Estatein team guided us through the entire buying process. Their knowledge and commitment to our needs were impressive. Thank you for your support!', 'John Mans', 'USA, Nevada', 'avatar-john.webp' ),
	);
	foreach ( $tests as $t ) {
		gm_seed_post( 'testimonial', $t[0], array( 'post_content' => $t[1] ), array( 'person_name' => $t[2], 'person_location' => $t[3], 'rating' => 5 ), array(), $t[4] );
	}

	
	$faqs = array(
		array( 'How do I search for properties on Estatein?', 'Learn how to use our user-friendly search tools to find properties that match your criteria.', 'Head to the Properties page and use the search bar and filters (location, property type, price range, size and build year). Results update as you choose, and every listing links to a full details page with photos and a pricing breakdown.' ),
		array( 'What documents do I need to sell my property through Estatein?', 'Find out about the necessary documentation for listing your property with us.', 'Typically you will need proof of ownership, a recent property tax statement, any HOA documents and recent utility bills. Our team will confirm the exact list for your property during your free valuation.' ),
		array( 'How can I contact an Estatein agent?', 'Discover the different ways you can get in touch with our experienced agents.', 'Use the form on the Contact Us page, email us, or call our main office. You can also use the “Say Hello” button on any team member card on the About page.' ),
		array( 'Does Estatein help with mortgages?', 'See how we work with lenders to make financing simpler for you.', 'We do not lend directly, but we partner with trusted lenders and can introduce you to one. Each listing includes an estimated cost breakdown so you can plan your down payment and monthly expenses.' ),
		array( 'Can I arrange a viewing?', 'Book a visit and see your future home in person.', 'Absolutely. Fill in the inquiry form on any property page and mention your preferred dates. An agent will confirm a time and accompany you on the viewing.' ),
		array( 'What fees does Estatein charge?', 'Understand our transparent pricing before you commit.', 'Fees depend on the service you choose. Buyer services are typically free to the buyer; selling and management services are quoted up-front, with no hidden charges.' ),
	);
	foreach ( $faqs as $i => $f ) {
		gm_seed_post( 'faq', $f[0], array( 'post_excerpt' => $f[1], 'post_content' => $f[2], 'menu_order' => $i ) );
	}

	
	foreach ( array( array( 'Max Mitchell', 'Founder', 'team-max.webp' ), array( 'Sarah Johnson', 'Chief Real Estate Officer', 'team-sarah.webp' ), array( 'David Brown', 'Head of Property Management', 'team-david.webp' ), array( 'Michael Turner', 'Legal Counsel', 'team-michael.webp' ) ) as $i => $m ) {
		gm_seed_post( 'team_member', $m[0], array( 'menu_order' => $i ), array( 'role' => $m[1] ), array(), $m[2] );
	}

	
	gm_seed_post( 'client', 'ABC Corporation', array( 'menu_order' => 0, 'post_content' => "Estatein's expertise in finding the perfect office space for our expanding operations was invaluable. They truly understand our business needs." ), array( 'since' => '2019', 'domain' => 'Commercial Real Estate', 'category' => 'Luxury Home Development', 'url' => 'https://example.com' ) );
	gm_seed_post( 'client', 'GreenTech Enterprises', array( 'menu_order' => 1, 'post_content' => "Estatein's ability to identify prime retail locations helped us expand our brand presence. They are a trusted partner in our growth." ), array( 'since' => '2018', 'domain' => 'Commercial Real Estate', 'category' => 'Retail Space', 'url' => 'https://example.com' ) );

	
	gm_seed_post( 'office', 'Main Headquarters', array( 'menu_order' => 0, 'post_content' => 'Our main headquarters serve as the heart of Estatein. Located in the bustling city center, this is where our core team of experts operates, driving the excellence and innovation that define us.' ), array( 'address' => '123 Estatein Plaza, City Center, Metropolis', 'email' => 'info@estatein.com', 'phone' => '+1 (123) 456-7890', 'city' => 'Metropolis', 'map_url' => 'https://maps.google.com/?q=Metropolis' ), array( 'office_type' => 'Regional' ) );
	gm_seed_post( 'office', 'Regional Offices', array( 'menu_order' => 1, 'post_content' => "Estatein's presence extends to multiple regions, each with its own dynamic real estate landscape. Discover our regional offices, staffed by local experts who understand the nuances of their respective markets." ), array( 'address' => '456 Urban Avenue, Downtown District, Metropolis', 'email' => 'info@restatein.com', 'phone' => '+1 (123) 628-7890', 'city' => 'Metropolis', 'map_url' => 'https://maps.google.com/?q=Metropolis' ), array( 'office_type' => 'Regional' ) );
	gm_seed_post( 'office', 'International Offices', array( 'menu_order' => 2, 'post_content' => 'Our international team supports overseas buyers and investors, with local partners who handle currency, legal and relocation questions from first call to closing day.' ), array( 'address' => '789 Harbour Street, Waterfront District, Metropolis', 'email' => 'global@estatein.com', 'phone' => '+1 (123) 777-7890', 'city' => 'Metropolis', 'map_url' => 'https://maps.google.com/?q=Metropolis' ), array( 'office_type' => 'International' ) );

	
	$services = array(
		'selling'    => array(
			array( 'Valuation Mastery', 'chart', 'Discover the true worth of your property with our expert valuation services.' ),
			array( 'Strategic Marketing', 'pie', 'Selling a property requires more than just a listing; it demands a strategic marketing approach.' ),
			array( 'Negotiation Wizardry', 'database', 'Negotiating the best deal is an art, and our negotiation experts are masters of it.' ),
			array( 'Closing Success', 'megaphone', 'A successful sale is not complete until the closing. We guide you through the intricate closing process.' ),
		),
		'management' => array(
			array( 'Tenant Harmony', 'apps', 'Our Tenant Management services ensure that your tenants have a smooth and reducing vacancies.' ),
			array( 'Maintenance Ease', 'tag', 'Say goodbye to property maintenance headaches. We handle all aspects of property upkeep.' ),
			array( 'Financial Peace of Mind', 'sparkles', 'Managing property finances can be complex. Our financial experts take care of rent collection' ),
			array( 'Legal Guardian', 'sun', 'Stay compliant with property laws and regulations effortlessly.' ),
		),
		'investment' => array(
			array( 'Market Insight', 'sparkles', 'Stay ahead of market trends with our expert Market Analysis. We provide in-depth insights into real estate market conditions' ),
			array( 'ROI Assessment', 'chart', 'Make investment decisions with confidence. Our ROI Assessment services evaluate the potential returns on your investments' ),
			array( 'Customized Strategies', 'apps', 'Every investor is unique, and so are their goals. We develop Customized Investment Strategies tailored to your specific needs' ),
			array( 'Diversification Mastery', 'database', 'Diversify your real estate portfolio effectively. Our experts guide you in spreading your investments across various property types and locations' ),
		),
	);
	foreach ( $services as $slug => $list ) {
		foreach ( $list as $i => $s ) {
			gm_seed_post( 'service', $s[0], array( 'post_excerpt' => $s[2], 'menu_order' => $i ), array( 'icon' => $s[1] ), array( 'service_group' => $slug ) );
		}
	}

	
	$about = array(
		'values'       => array(
			array( 'Trust', 'star', 'Trust is the cornerstone of every successful real estate transaction.' ),
			array( 'Excellence', 'cap', 'We set the bar high for ourselves. From the properties we list to the services we provide.' ),
			array( 'Client-Centric', 'users', 'Your dreams and needs are at the center of our universe. We listen, understand.' ),
			array( 'Our Commitment', 'star', 'We are dedicated to providing you with the highest level of service, professionalism, and support.' ),
		),
		'achievements' => array(
			array( '3+ Years of Excellence', '', "With over 3 years in the industry, we've amassed a wealth of knowledge and experience, becoming a go-to resource for all things real estate." ),
			array( 'Happy Clients', '', 'Our greatest achievement is the satisfaction of our clients. Their success stories fuel our passion for what we do.' ),
			array( 'Industry Recognition', '', "We've earned the respect of our peers and industry leaders, with accolades and awards that reflect our commitment to excellence." ),
		),
		'steps'        => array(
			array( 'Discover a World of Possibilities', 'Step 01', 'Your journey begins with exploring our carefully curated property listings. Use our intuitive search tools to filter properties based on your preferences, including location, type, size, and budget.' ),
			array( 'Narrowing Down Your Choices', 'Step 02', "Once you've found properties that catch your eye, save them to your account or make a shortlist. This allows you to compare and revisit your favorites as you make your decision." ),
			array( 'Personalized Guidance', 'Step 03', 'Have questions about a property or need more information? Our dedicated team of real estate experts is just a call or message away.' ),
			array( 'See It for Yourself', 'Step 04', "Arrange viewings of the properties you're interested in. We'll coordinate with the property owners and accompany you to ensure you get a firsthand look at your potential new home." ),
			array( 'Making Informed Decisions', 'Step 05', 'Before making an offer, our team will assist you with due diligence, including property inspections, legal checks, and market analysis. We want you to be fully informed and confident in your choice.' ),
			array( 'Getting the Best Deal', 'Step 06', "We'll help you negotiate the best terms and prepare your offer. Our goal is to secure the property at the right price and on favorable terms." ),
		),
	);
	foreach ( $about as $slug => $list ) {
		foreach ( $list as $i => $a ) {
			$meta = 'steps' === $slug ? array( 'step_label' => $a[1] ) : ( $a[1] ? array( 'icon' => $a[1] ) : array() );
			gm_seed_post( 'about_item', $a[0], array( 'post_excerpt' => $a[2], 'menu_order' => $i ), $meta, array( 'about_group' => $slug ) );
		}
	}

	update_option( 'gm_seeded', 1 );
	flush_rewrite_rules( false );
}
