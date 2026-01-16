<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Visita - Porteiro</title>
    <link rel="stylesheet" href="../../../assets/css/colors.css">
    <link rel="stylesheet" href="../../../assets/css/porteiro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font-family);
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('../../img/coco.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--color-dark);
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .form-card {
            background-color: rgba(255, 255, 255, 0.98);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            border-top: 4px solid var(--color-primary);
        }

        .form-card h1 {
            color: var(--color-primary-dark);
            margin-bottom: 10px;
            font-size: var(--font-size-2xl);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-card h1 i {
            color: var(--color-primary);
        }

        .form-subtitle {
            color: var(--color-gray);
            margin-bottom: 30px;
            font-size: var(--font-size-sm);
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #e5e7eb;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title {
            color: var(--color-primary-dark);
            font-weight: 600;
            margin-bottom: 20px;
            font-size: var(--font-size-lg);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: var(--color-primary);
            font-size: 1.3rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            color: var(--color-dark);
            font-weight: 500;
            margin-bottom: 8px;
            font-size: var(--font-size-sm);
        }

        .form-group label .required {
            color: var(--color-error);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-family: var(--font-family);
            font-size: var(--font-size-base);
            transition: border-color 0.3s, box-shadow 0.3s;
            background-color: #f9fafb;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--color-primary);
            background-color: white;
            box-shadow: 0 0 0 3px rgba(152, 67, 215, 0.1);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--color-success);
            border: 1px solid var(--color-success);
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--color-error);
            border: 1px solid var(--color-error);
        }

        .alert i {
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .button-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: var(--font-size-base);
            font-weight: 600;
            transition: all 0.3s;
            font-family: var(--font-family);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit {
            background-color: var(--color-primary);
            color: white;
        }

        .btn-submit:hover {
            background-color: var(--color-primary-dark);
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-cancel {
            background-color: var(--color-light-gray);
            color: var(--color-dark);
            border: 1px solid #d1d5db;
        }

        .btn-cancel:hover {
            background-color: #e5e7eb;
            box-shadow: var(--shadow-sm);
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 20px;
            }

            .form-card h1 {
                font-size: var(--font-size-xl);
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <h1>
                <i class="fas fa-user-check"></i> Registrar Visita
            </h1>
            <p class="form-subtitle">Preencha os dados da visita e do visitante</p>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo htmlspecialchars($_GET['success']); ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($_GET['erro']); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="../../controller/Porteiro/visita.php">
                <!-- Seção: Dados do Visitante -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="fas fa-id-card"></i> Dados do Visitante
                    </h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome_visitante">
                                Nome do Visitante <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="nome_visitante" 
                                name="nome_visitante" 
                                required 
                                placeholder="Nome completo do visitante"
                            >
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo_documento">
                                Tipo de Documento <span class="required">*</span>
                            </label>
                            <select id="tipo_documento" name="tipo_documento" required>
                                <option value="">Selecione o tipo</option>
                                <option value="CPF">BI</option>
                                <option value="Carta De Conducao">Carta de condução</option>
                                <option value="Passaporte">Passaporte</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="documento">
                                Número do Documento <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="documento" 
                                name="documento" 
                                required 
                                placeholder="Ex: 123.456.789-00"
                            >
                        </div>
                    </div>
                </div>

                <!-- Seção: Dados do Agendamento -->
                <div class="form-section">
                    <h2 class="section-title">
                        <i class="fas fa-calendar-alt"></i> Dados do Agendamento
                    </h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="id_morador">
                                Selecione o Morador <span class="required">*</span>
                            </label>
                            <select id="id_morador" name="id_morador" required>
                                <option value="">-- Escolha um morador --</option>
                                <?php
                                require_once '../../data/conector.php';
                                
                                try {
                                    $conector = new Conector();
                                    $conexao = $conector->getConexao();
                                    
                                    $query = "SELECT m.id_morador, m.nome, u.numero 
                                             FROM Morador m 
                                             LEFT JOIN Unidade u ON m.id_unidade = u.id_unidade 
                                             ORDER BY m.nome ASC";
                                    
                                    $resultado = $conexao->query($query);
                                    
                                    if ($resultado && $resultado->num_rows > 0) {
                                        while ($row = $resultado->fetch_assoc()) {
                                            $unidade = $row['numero'] ? " (Apt. " . htmlspecialchars($row['numero']) . ")" : "";
                                            echo "<option value=\"" . $row['id_morador'] . "\">" 
                                                 . htmlspecialchars($row['nome']) . $unidade 
                                                 . "</option>";
                                        }
                                    }
                                } catch (Exception $e) {
                                    // Silenciosamente continua
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="data">
                                Data da Visita <span class="required">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="data" 
                                name="data" 
                                required
                                min="<?php echo date('Y-m-d'); ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label for="hora">
                                Hora da Visita <span class="required">*</span>
                            </label>
                            <input 
                                type="time" 
                                id="hora" 
                                name="hora" 
                                required
                            >
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="motivo">
                                Motivo da Visita
                            </label>
                            <textarea 
                                id="motivo" 
                                name="motivo" 
                                placeholder="Descreva brevemente o motivo da visita (opcional)"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="button-group">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save"></i> Registrar Visita
                    </button>
                    <button type="button" class="btn btn-cancel" onclick="window.history.back()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validação de data
        document.getElementById('data').addEventListener('change', function() {
            const data = new Date(this.value);
            const hoje = new Date();
            hoje.setHours(0, 0, 0, 0);
            
            if (data < hoje) {
                alert('A data não pode ser no passado!');
                this.value = '';
            }
        });
    </script>
</body>
</html>
