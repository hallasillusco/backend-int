import mysql.connector
import pandas as pd
from sklearn.linear_model import LinearRegression
import json
from datetime import datetime
from dateutil.relativedelta import relativedelta

# Conexión a base de datos
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",  # Pon tu contraseña si tienes
    database="intecsadb"  # Cambia si tu base se llama diferente
)

# Consulta de ventas por mes
query = """
SELECT DATE_FORMAT(fecha_registro, '%Y-%m') as mes, SUM(total) as ventas
FROM ventas
GROUP BY mes
ORDER BY mes ASC;
"""

df = pd.read_sql(query, conn)
conn.close()

# Prepara los datos para regresión
df['mes_n'] = range(1, len(df) + 1)
X = df[['mes_n']]
y = df['ventas']

modelo = LinearRegression()
modelo.fit(X, y)

# Predecir los siguientes 3 meses
n_predicciones = 3
futuro_X = pd.DataFrame({'mes_n': range(len(df)+1, len(df)+n_predicciones+1)})
predicciones = modelo.predict(futuro_X)

# Generar fechas futuras
ultimo_mes = datetime.strptime(df['mes'].iloc[-1], '%Y-%m')
meses_futuros = [(ultimo_mes + relativedelta(months=i)).strftime('%Y-%m') for i in range(1, n_predicciones+1)]

# Dataframe con predicciones futuras
df_pred = pd.DataFrame({
    'mes': meses_futuros,
    'ventas': [None]*n_predicciones,
    'ventas_previstas': predicciones
})

# Combinar ventas reales y previstas
df['ventas_previstas'] = None
df_final = pd.concat([df, df_pred], ignore_index=True)

# Guardar como JSON para Laravel
output_path = 'storage/prediccion/resultados.json'
df_final.to_json(output_path, orient='records', indent=2)

print("✅ Predicción generada:", output_path)
