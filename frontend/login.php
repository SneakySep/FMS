<?php
session_start();
require_once 'src/helpers/api_helper.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Email and Password should not empty";
    } else {
        $login_payload = [
            'email' => $email,
            'password' => $password
        ];

        // 1. I-post sa tamang FastAPI router endpoint gamit ang JSON false
        $response = make_api_request('/api/auth/login', 'POST', $login_payload, false);

        // 2. Case A: Kung OTP flow ang setup (Step 1 Complete)
        if ($response['status_code'] == 200 && isset($response['data']['status']) && $response['data']['status'] === 'otp_sent') {
            $_SESSION["temp_email"] = $email;
            header("Location: otp_verification.php");
            exit();
        } 
        // 3. Case B: Direct Token Response Kung walang OTP
        elseif ($response['status_code'] == 200 && isset($response['data']['access_token'])) {
            $token = $response['data']['access_token'];
            $_SESSION["access_token"] = $token;

            // Decode JWT Payload para sa User info at Role
            $token_parts = explode('.', $token);
            if (count($token_parts) === 3) {
                $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $token_parts[1])), true);
                $_SESSION["email"] = $payload["email"] ?? $email;
                $_SESSION["role"] = $payload["role"] ?? "customer";
            }

            if ($_SESSION["role"] === 'sales' || $_SESSION["role"] === 'sales_agent') {
                header("Location: src/views/sales_agent/dashboard.php"); // Path ng Sales Dashboard mo

            } else if($_SESSION["role"] === 'admin' || $_SESSION["role"] === 'administrator') {
                header("Location: src/views/admin/dashboard.php"); // Path ng Admin Dashboard mo
            } 
            else if($_SESSION["role"] === 'customer') {
                header("Location: src/views/customer/dashboard.php"); // Path ng Customer Dashboard mo
            } 
            else {
                $error = "Unauthorized role. Please contact support.";
            }
            exit();
        } 
        // 4. Handling ng Error
        else {
            $error = $response['error'] ?? ($response['data']['detail'] ?? "Maling email o password.");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <?php include_once 'src/components/head.php'; ?>
</head>
<body class="bg-slate-950 text-slate-900 font-sans antialiased min-h-screen flex flex-col justify-between overflow-x-hidden">

    <?php include_once 'src/components/header.php'; ?>

    <!-- MAIN AUTH SECTION WITH BACKGROUND -->
    <main class="relative w-full min-h-[calc(100vh-73px)] flex items-center justify-center overflow-hidden bg-slate-950 text-white">
        
        <div class="absolute inset-0 opacity-30">
            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1600&q=80" alt="Warehouse" class="w-full h-full object-cover">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/90 via-slate-950/70 to-brand-darkblue/40 z-10"></div>

        <!-- Foreground Content -->
        <div class="relative z-20 max-w-7xl mx-auto px-6 w-full py-12 lg:py-16">
            <div class="flex justify-center">

                <!-- Agent Login Card -->
                <div class="w-full max-w-md">
                    <div class="bg-slate-950/80 border border-white/15 p-8 sm:p-10 rounded-3xl backdrop-blur-2xl shadow-2xl relative">
                        
                        <div class="text-center mb-8">
                            <div class="w-12 h-12 bg-brand-blue/20 border border-brand-blue/30 rounded-2xl flex items-center justify-center text-brand-blue text-xl mx-auto mb-3 shadow-lg">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <h3 class="text-2xl font-extrabold text-white">PRIORITY HANDLING</h3>
                            <p class="text-slate-400 text-xs mt-1">Enter your internal credentials to access the console</p>
                        </div>

                        <?php include_once 'src/components/error_handling.php'; ?>
                        
                        <form method="POST" action="login.php" class="space-y-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" required
                                           class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all"
                                           placeholder="name@priority-ph.com">
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1.5">
                                    <label class="block text-xs font-semibold text-slate-300">Password</label>
                                    
                                </div>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-key"></i>
                                    </span>
                                    <input type="password" name="password" required  
                                           class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all"
                                           placeholder="••••••••">
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-brand-blue hover:bg-brand-darkblue text-white font-semibold text-xs py-3.5 rounded-xl transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-right-to-bracket"></i> Sign In 
                            </button>
                        </form>


                    </div>
                </div>

            </div>
        </div>

    </main>

    <footer class="bg-slate-950 text-slate-500 py-8 px-6 text-xs border-t border-white/10 relative z-20">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <p>&copy; 2026 Priority Handling Logistics Inc. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="javascript:void(0)" onclick="openPrivacyModal()" class="hover:text-white transition-colors">Privacy Policy (RA 10173)</a>
                <a href="javascript:void(0)" onclick="openTermsModal()" class="hover:text-white transition-colors">Terms of Service (PH Law)</a>
            </div>
        </div>
    </footer>

    <!-- MODALS -->
    <!-- PRIVACY POLICY MODAL -->
    <div id="privacyModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-[70] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-3xl w-full max-h-[85vh] flex flex-col text-slate-200 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-slate-950">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-brand-blue text-lg"></i>
                    <div>
                        <h3 class="text-base font-bold text-white">Privacy Policy</h3>
                        <span class="text-[10px] text-slate-400">Compliant with Republic Act No. 10173 (Philippine Data Privacy Act of 2012)</span>
                    </div>
                </div>
                <button onclick="closePrivacyModal()" class="text-slate-400 hover:text-white p-2 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4 text-xs leading-relaxed text-slate-300">
                <p>Priority Handling Logistics Inc. (&quot;Carrier,&quot; &quot;we,&quot; &quot;us,&quot; or &quot;our&quot;) is committed to protecting your personal data in compliance with Republic Act No. 10173, also known as the Data Privacy Act of 2012 (DPA). This Privacy Policy explains how we collect, use, disclose, and safeguard your personal information when you use our website, online forms, and logistics or freight forwarding services.</p>

                <p class="font-semibold text-white">1. Information We Collect</p>
                <p>We collect personal data necessary to provide freight forwarding, courier, and customs services. This includes:</p>
                <p class="pl-4"><strong class="text-white">Account &amp; Contact Data:</strong> Name, business address, email address, phone number, company name, and job title.</p>
                <p class="pl-4"><strong class="text-white">Shipment &amp; Consignment Data:</strong> Consignor and Consignee (recipient) details, including physical addresses, contact phone numbers, customs declarations, descriptions of goods, and order histories.</p>
                <p class="pl-4"><strong class="text-white">Payment &amp; Financial Data:</strong> Billing addresses, transaction logs, and payment details required to settle freight charges.</p>
                <p class="pl-4"><strong class="text-white">Automatically Collected Data:</strong> IP addresses, browser types, and usage logs collected through cookies when you visit our website.</p>
                <p><strong class="text-white">Note on Consignee Data:</strong> When you provide personal information regarding third parties (such as recipients or alternate contact persons), you warrant that you have obtained their explicit consent to share their information with us for delivery purposes.</p>

                <p class="font-semibold text-white">2. How We Use Your Personal Data</p>
                <p>We process personal data for the following lawful purposes:</p>
                <p class="pl-4"><strong class="text-white">Service Execution:</strong> Arranging, tracking, routing, and delivering shipments via our own network or through successive carriers, agents, and sub-contractors.</p>
                <p class="pl-4"><strong class="text-white">Regulatory Compliance:</strong> Completing customs declarations, tax reporting, and complying with local and international logistics laws (including trade regulations and conventions such as the Warsaw Convention).</p>
                <p class="pl-4"><strong class="text-white">Customer Support:</strong> Addressing inquiries, investigating non-deliveries, and processing damage or loss claims submitted under our Terms and Conditions.</p>
                <p class="pl-4"><strong class="text-white">Communications:</strong> Sending automated shipment status alerts, account updates, and promotional materials (only where express opt-in consent has been provided).</p>

                <p class="font-semibold text-white">3. Data Sharing and Third-Party Disclosures</p>
                <p>To fulfill our contractual duties, we may share personal data with trusted third parties under strict confidentiality agreements:</p>
                <p class="pl-4"><strong class="text-white">Sub-Contractors and Successive Carriers:</strong> Third-party logistics partners, air/sea freight carriers, driver networks, and local delivery agents necessary to complete transit.</p>
                <p class="pl-4"><strong class="text-white">Customs and Regulatory Authorities:</strong> Government agencies, border control, and customs offices in origin and destination countries to clear consignments.</p>
                <p class="pl-4"><strong class="text-white">Service Providers:</strong> IT support, web hosting, payment processors, and audit providers assisting our business operations.</p>
                <p>We do not sell, rent, or trade your personal data to third parties for marketing purposes.</p>

                <p class="font-semibold text-white">4. International Data Transfers</p>
                <p>Because freight logistics frequently involves international transit, your personal data and shipment information may be transferred across international borders to destination countries, overseas customs authorities, or foreign carrier networks in order to complete delivery.</p>

                <p class="font-semibold text-white">5. Data Retention and Security</p>
                <p>We implement appropriate organizational, physical, and technical security measures to protect your personal information from unauthorized access, alteration, or disclosure. Personal data is retained only for as long as necessary to fulfill the purposes outlined in this policy, settle accounts, resolve disputes, or comply with statutory retention requirements under Philippine law.</p>

                <p class="font-semibold text-white">6. Your Rights Under RA 10173</p>
                <p>Under the Data Privacy Act of 2012, you have the right to:</p>
                <p class="pl-4"><strong class="text-white">Access</strong> the personal information we hold about you.</p>
                <p class="pl-4"><strong class="text-white">Rectify or correct</strong> inaccurate or outdated data.</p>
                <p class="pl-4"><strong class="text-white">Erasure or Blocking:</strong> Request the suspension, withdrawal, or removal of your personal data from our systems (subject to legal or contractual limitations, such as active freight contracts or customs retention mandates).</p>
                <p class="pl-4"><strong class="text-white">Withdraw Consent:</strong> Opt out of promotional communications at any time.</p>

                <p class="font-semibold text-white">7. Contact Our Data Protection Officer (DPO)</p>
                <p>For inquiries, requests to exercise your data privacy rights, or feedback regarding our privacy practices, please contact our Data Protection Officer:</p>
                <p class="pl-4"><strong class="text-white">Company Name:</strong> Priority Handling Logistics Inc.</p>
                <p class="pl-4"><strong class="text-white">Email:</strong> cs@priority-ph.com</p>
                <p class="pl-4"><strong class="text-white">Subject Line:</strong> Attn: Data Protection Officer / Privacy Request</p>
            </div>
            <div class="p-4 border-t border-white/10 bg-slate-950 flex justify-end">
                <button onclick="closePrivacyModal()" class="bg-brand-blue hover:bg-brand-darkblue text-white px-5 py-2 rounded-lg text-xs font-semibold">I Understand</button>
            </div>
        </div>
    </div>
    <!-- TERMS & CONDITIONS MODAL -->
    <div id="termsModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-[70] flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-3xl w-full max-h-[85vh] flex flex-col text-slate-200 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-slate-950">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-file-contract text-brand-blue text-lg"></i>
                    <div>
                        <h3 class="text-base font-bold text-white">Terms and Conditions</h3>
                        <span class="text-[10px] text-slate-400">Governed by the Laws of the Republic of the Philippines</span>
                    </div>
                </div>
                <button onclick="closeTermsModal()" class="text-slate-400 hover:text-white p-2 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto space-y-4 text-xs leading-relaxed text-slate-300">
                <p class="font-semibold text-white">Effective Date: January 1, 2026</p>
                <p class="font-semibold text-white mb-2">TERMS AND CONDITIONS OF CARRIAGE</p>
                <p><strong class="text-white">1. In these conditions.</strong> The &quot;Carrier&quot; means Priority Handling Logistics, Inc. carrying on business in its own name and under any business name and unless the context otherwise requires its officers, servants, agents and sub-contractors THE CARRIES IS NOT A COMMON CARRIER and will accept no liability as such.</p>
                <p><strong class="text-white">2. The Carrier reserves the right to:</strong></p>
                <p class="pl-4">a) refuse the carriage or transport of goods to its destination,</p>
                <p class="pl-4">b) carry consignor's documents or goods by any route and procedure, and by successive carriers and according to its handling storage and transportation methods, and</p>
                <p class="pl-4">c) to inspect any goods consigned to ensure that they are capable of carriage to the countries of destination within the standard operating procedures, customs declarations, and handling methods of the Carrier.</p>
                <p><strong class="text-white">4. The Consignor is responsible</strong> for the packing of the goods including the packing in any container that may be supplied to the Consignor by the Carrier. The Carrier accepts no responsibility for loss or damage or to the goods caused by inadequate of inappropriate packaging. It is the responsibility of the Consignor to address adequately each consignment to enable effective delivery to be made. The Carrier shall not be liable for delay in forwarding or delivery resulting from the Consignor's failure to comply with its obligation in this respect.</p>
                <p><strong class="text-white">5. The Consignor warrants</strong> that it is authorized to accept and is accepting these Conditions on behalf of itself but also as an agent for and on behalf of all other person who are or may hereafter become interested in the goods. The Consignor indemnifies the Carrier against any damages, costs and expenses resulting from any breach of this warranty.</p>
                <p><strong class="text-white">6. The Carrier shall have a lien</strong> on all goods shipped, for freight charges and/or advance for other charges of any kind arising out of transportation hereunder and may refuse to surrender possession of the goods until such charges are paid.</p>
                <p><strong class="text-white">7. The Carrier is liable</strong> for damage sustained in the event of the destruction or loss of, or of damage to any cargo, the occurrence which caused the damage so sustained takes place during carriage and the Carrier is also liable for damage occasioned by delay in the carriage of cargo. The Carrier is not liable if it proves that it and its servants and agents have taken all necessary measures to avoid the damage or that it was impossible for it or them to take such measures. PROVIDED ALWAYS THAT the liability of the Carrier under these conditions shall be limited to the payment by the Carrier by way of damages of a sum of not exceeding US$100.00 or its local currency equivalent and Pesos 100.00 for international and domestic carriage respectively per consignment or in the case where transit insurance is affected the amount payable thereunder in the event of loss or damage to the goods.</p>
                <p><strong class="text-white">8. Any claim brought</strong> by a customer against the Carrier hereunder in respect of duties and liabilities must be notified by the customer to an office of the Carrier with in 48 hours and in writing within 14 days of the day when the goods should have reached their destination. No claim for loss or damage may be entertained if o in is filed beyond the allowable period or until the transportation charges have been paid.</p>
                <p><strong class="text-white">9. The Carrier will not carry</strong> dangerous, hazardous, combustible or explosive materials, gold and silver bullion, coin, dust, cyanides, precipitates or any form of uncoined gold and silver bullion, platinum and other precious metals, precious and semi-precious stones, currency (paper or coin) of any nationality, negotiable securities, stocks, bonds, certificates, stamps, blank or endorsed bank cheques, money orders or travelers cheques, antiques, works of art, livestock, plants, drugs, pharmaceuticals, liquor, firearms, tobacco, foodstuff, IATA restricted articles or perishables and in the event that the Consignor should consign such items. with the Carrier the Consignor indemnities the Carrier for all claims, damages costs and expenses which may arise as a result of such carriage and the Carrier shall have the right to abandon carriage of the same immediately upon Carrier having knowledge that the goods infringe this condition.</p>
                <p><strong class="text-white">10. If the carriage involves</strong> an ultimate destination or stop in a country other than the country of departure, the Warsaw Convention may be applicable and the Convention governs and in most cases limits the liability of carriers in respect of lost of or damage to cargo.</p>
            </div>
            <div class="p-4 border-t border-white/10 bg-slate-950 flex justify-end">
                <button onclick="closeTermsModal()" class="bg-brand-blue hover:bg-brand-darkblue text-white px-5 py-2 rounded-lg text-xs font-semibold">Accept Terms</button>
            </div>
        </div>
    </div>

    <script src="assets/js/footer.js"></script>
</body>
</html>
