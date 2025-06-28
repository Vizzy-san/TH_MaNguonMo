<?php

// Change from app/core/Database.php to app/config/database.php
require_once 'app/config/database.php';

class PaymentModel {
    private $db;

    public function __construct() {
        // Initialize with the connection, not the Database class itself
        $this->db = (new Database())->getConnection();
    }

    public function createPayment($orderId, $amount, $description = '', $customerId = null) {
        try {
            $sql = "INSERT INTO payments (order_id, amount, description, customer_id) 
                   VALUES (:order_id, :amount, :description, :customer_id)";
            
            // Use PDO methods directly since $this->db is now a PDO connection
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':order_id', $orderId);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':customer_id', $customerId);
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('PaymentModel::createPayment Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updatePaymentStatus($paymentId, $status, $transactionId = null, $responseData = null) {
        try {
            $sql = "UPDATE payments SET status = :status";
            
            if ($transactionId) {
                $sql .= ", transaction_id = :transaction_id";
            }
            
            if ($responseData) {
                $sql .= ", response_data = :response_data";
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $paymentId);
            
            if ($transactionId) {
                $stmt->bindParam(':transaction_id', $transactionId);
            }
            
            if ($responseData) {
                $stmt->bindParam(':response_data', $responseData);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('PaymentModel::updatePaymentStatus Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getPaymentById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM payments WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log('PaymentModel::getPaymentById Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getPaymentsByOrderId($orderId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM payments WHERE order_id = :order_id");
            $stmt->bindParam(':order_id', $orderId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log('PaymentModel::getPaymentsByOrderId Error: ' . $e->getMessage());
            return false;
        }
    }

    public function savePayOSResponse($paymentId, $transactionId, $qrCode, $checkoutUrl = null, $deeplink = null, $paymentLinkId = null) {
        try {
            $sql = "UPDATE payments SET 
                    transaction_id = :transaction_id,
                    qr_code = :qr_code,
                    checkout_url = :checkout_url,
                    deeplink = :deeplink,
                    payment_linkid = :payment_linkid,
                    status = 'awaiting_payment'
                    WHERE id = :id";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':transaction_id', $transactionId);
            $stmt->bindParam(':qr_code', $qrCode);
            $stmt->bindParam(':checkout_url', $checkoutUrl);
            $stmt->bindParam(':deeplink', $deeplink);
            $stmt->bindParam(':payment_linkid', $paymentLinkId);
            $stmt->bindParam(':id', $paymentId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('PaymentModel::savePayOSResponse Error: ' . $e->getMessage());
            return false;
        }
    }
}
